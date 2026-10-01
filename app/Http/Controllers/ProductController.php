<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Typesense\Client;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::select('id', 'name', 'slug')
            ->get();
        $brands = Brand::select('id', 'name', 'slug')
            ->get();
        $priceRange = Product::query()
            ->where('price', '>', 0)
            ->selectRaw('MIN(price) as minprice, MAX(price) as maxprice')
            ->first();

        return view('products.index', [
            'categories' => $categories,
            'brands' => $brands,
            'minprice' => $priceRange?->minprice,
            'maxprice' => $priceRange?->maxprice,
        ]);
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'brand', 'images', 'comments.user'])
            ->where('slug', $slug)
            // ->active()
            ->firstOrFail();

        $similarProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->take(10)
            ->get();

        $brandsimilarProducts = Product::with(['brand', 'images'])
            ->where('brand_id', $product->brand_id)
            ->active()
            ->inStock()
            ->take(10)
            ->get();

        // Aggregate purchases at SQL level. The previous implementation loaded
        // every OrderItem and then every related Order/User into PHP memory.
        $purchaseHistory = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.avatar',
                'users.instagram_account',
                'users.facebook_account',
                'users.tiktok_account',
                'users.x_account',
                DB::raw('SUM(order_items.quantity) as quantity'),
            ])
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $product->id)
            ->groupBy(
                'users.id',
                'users.name',
                'users.avatar',
                'users.instagram_account',
                'users.facebook_account',
                'users.tiktok_account',
                'users.x_account'
            )
            ->get()
            ->map(static function ($user) {
                return (object) [
                    'user' => $user,
                    'quantity' => (int) $user->quantity,
                ];
            });

        // Avoid a synchronous SQL UPDATE on every product page request.
        Redis::hIncrBy('product:view:deltas', (string) $product->id, 1);

        return view('products.show', compact('product', 'similarProducts', 'brandsimilarProducts', 'purchaseHistory'));
    }

    public function search(Request $request)
    {
        $client = new Client(config('scout.typesense.client-settings'));

        $searchParameters = [
            'q' => $request->input('search', ''),
            'query_by' => 'name,description,tags',
            'filter_by' => $this->buildFilters($request),
            'sort_by' => $this->buildSort($request->input('sort', 'newest')),
            'per_page' => 12,
            'page' => $request->input('page', 1),
            'use_cache' => true,
            'cache_ttl' => 60,
        ];

        $result = $client->collections['products']->documents->search($searchParameters);

        $ids = collect($result['hits'])
            ->pluck('document.id')
            ->map(static fn ($id) => (int) $id)
            ->values();

        $productsById = Product::query()
            ->with(['images', 'brand', 'category'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $products = $ids
            ->map(static fn (int $id) => $productsById->get($id))
            ->filter()
            ->values();

        $pagination = view('partials.pagination', ['paginator' => $products])->render();

        return response()->json([
            'products' => view('partials.product-list', ['products' => $products])->render(),
            'pagination' => $pagination,
        ]);
    }

    private function buildFilters(Request $request)
    {
        $filters = [];

        $categories = array_values(array_filter((array) $request->input('categories', []), fn ($id) => filter_var($id, FILTER_VALIDATE_INT) !== false && (int) $id > 0));
        if ($categories) {
            $filters[] = 'category_id:=['.implode(',', array_map('intval', $categories)).']';
        }

        $brands = array_values(array_filter((array) $request->input('brands', []), fn ($id) => filter_var($id, FILTER_VALIDATE_INT) !== false && (int) $id > 0));
        if ($brands) {
            $filters[] = 'brand_id:=['.implode(',', array_map('intval', $brands)).']';
        }

        $priceMin = $request->input('price_min');
        if (is_numeric($priceMin) && (float) $priceMin >= 0) {
            $filters[] = 'price:>='.number_format((float) $priceMin, 2, '.', '');
        }

        $priceMax = $request->input('price_max');
        if (is_numeric($priceMax) && (float) $priceMax >= 0) {
            $filters[] = 'price:<='.number_format((float) $priceMax, 2, '.', '');
        }

        if ($request->input('only_active')) {
            $filters[] = 'is_active:=true';
        }

        if ($request->input('only_in_stock')) {
            $filters[] = 'stock:>0';
        }

        return implode(' && ', $filters);
    }

    private function buildSort($sort)
    {
        switch ($sort) {
            case 'price_asc':
                return 'price:asc';
            case 'price_desc':
                return 'price:desc';
            case 'newest':
            default:
                return 'created_at:desc';
        }
    }

    public function rate(Request $request, Product $product)
    {
        if (! $product->hasBeenPurchasedBy(auth()->user())) {
            return back()->with('error', 'You can only rate products you have purchased.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $rating = ProductRating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        // Update product's average rating
        $avgRating = ProductRating::where('product_id', $product->id)->avg('rating');
        $product->update(['rating' => round($avgRating, 1)]);

        return back()->with('success', 'Thank you for your rating!');
    }
}
