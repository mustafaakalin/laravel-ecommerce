<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\BestSellingProductsComponentForMobileResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BestSellingProductsComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $products = Cache::remember('mobile_best_selling_products', self::CACHE_TTL, function () {
            $bestSellers = DB::table('order_items')
                ->select([
                    'order_items.product_id',
                    DB::raw('SUM(order_items.quantity) as total_sales')
                ])
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', 'delivered')
                ->groupBy('order_items.product_id')
                ->orderByDesc('total_sales')
                ->limit(10)
                ->get();

            $productIds = $bestSellers->pluck('product_id');
            $salesMap = $bestSellers->pluck('total_sales', 'product_id');

            return Product::query()
                ->whereIn('id', $productIds)
                ->with(['category:id,name,slug', 'media', 'ratings'])
                ->where('is_active', true)
                ->get()
                ->map(function ($product) use ($salesMap) {
                    $product->sales = $salesMap[$product->id] ?? 0;
                    return $product;
                })
                ->sortByDesc('sales')
                ->values();
        });

        return BestSellingProductsComponentForMobileResource::collection($products);
    }
}
