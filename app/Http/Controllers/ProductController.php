<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductRating;
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
        $minprice = Product::where('price', '>', 0)->min('price');
        $maxprice = Product::where('price', '>', 0)->max('price');
        $products = Product::all();

        return view('products.index', compact('products', 'categories', 'brands', 'minprice', 'maxprice'));
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

        $purchaseHistory = OrderItem::with('order.user')
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'user' => $item->order->user,
                    'quantity' => $item->quantity,
                ];
            });

        $product->incrementViewCount();

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
        ];

        $result = $client->collections['products']->documents->search($searchParameters);

        $products = collect($result['hits'])->map(function ($hit) {
            return Product::find($hit['document']['id']);
        });

        $pagination = view('partials.pagination', ['paginator' => $products])->render();

        return response()->json([
            'products' => view('partials.product-list', ['products' => $products])->render(),
            'pagination' => $pagination,
        ]);
    }

    private function buildFilters(Request $request)
    {
        $filters = [];

        if ($categories = $request->input('categories', [])) {
            $filters[] = 'category_id:=['.implode(',', $categories).']';
        }

        if ($brands = $request->input('brands', [])) {
            $filters[] = 'brand_id:=['.implode(',', $brands).']';
        }

        if ($price_min = $request->input('price_min')) {
            $filters[] = 'price:>='.$price_min;
        }

        if ($price_max = $request->input('price_max')) {
            $filters[] = 'price:<='.$price_max;
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
