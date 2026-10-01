<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\PopularProductsHomepageComponentForMobileResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopularProductsHomepageComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600;

    public function index(): AnonymousResourceCollection
    {
        $products = Cache::remember(\App\Support\CacheKeys::mobile('popular-products'), self::CACHE_TTL, function () {
            $cartCounts = DB::table('cart_items')
                ->select('product_id', DB::raw('COUNT(*) as cart_items_count'))
                ->groupBy('product_id');

            $salesCounts = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'delivered')
                ->select('order_items.product_id', DB::raw('COUNT(*) as order_items_count'))
                ->groupBy('order_items.product_id');

            $commentCounts = DB::table('comments')
                ->select('product_id', DB::raw('COUNT(*) as comments_count'))
                ->groupBy('product_id');

            $likeCounts = DB::table('likes')
                ->select('product_id', DB::raw('COUNT(*) as likes_count'))
                ->groupBy('product_id');

            $ratingStats = DB::table('product_ratings')
                ->select(
                    'product_id',
                    DB::raw('COUNT(*) as ratings_count'),
                    DB::raw('AVG(rating) as ratings_avg_rating')
                )
                ->groupBy('product_id');

            $score = '(
                COALESCE(cart_metrics.cart_items_count, 0) * 0.15 +
                COALESCE(sales_metrics.order_items_count, 0) * 0.35 +
                COALESCE(comment_metrics.comments_count, 0) * 0.15 +
                COALESCE(like_metrics.likes_count, 0) * 0.15 +
                COALESCE(rating_metrics.ratings_avg_rating, 0) * 0.20
            )';

            return Product::query()
                ->select([
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.price',
                    'products.stock',
                    'products.category_id',
                    'products.discount',
                    'products.is_active',
                ])
                ->selectRaw('COALESCE(cart_metrics.cart_items_count, 0) as cart_items_count')
                ->selectRaw('COALESCE(sales_metrics.order_items_count, 0) as order_items_count')
                ->selectRaw('COALESCE(comment_metrics.comments_count, 0) as comments_count')
                ->selectRaw('COALESCE(like_metrics.likes_count, 0) as likes_count')
                ->selectRaw('COALESCE(rating_metrics.ratings_count, 0) as ratings_count')
                ->selectRaw('COALESCE(rating_metrics.ratings_avg_rating, 0) as ratings_avg_rating')
                ->selectRaw("ROUND({$score}, 1) as popularity_score")
                ->leftJoinSub($cartCounts, 'cart_metrics', function ($join) {
                    $join->on('products.id', '=', 'cart_metrics.product_id');
                })
                ->leftJoinSub($salesCounts, 'sales_metrics', function ($join) {
                    $join->on('products.id', '=', 'sales_metrics.product_id');
                })
                ->leftJoinSub($commentCounts, 'comment_metrics', function ($join) {
                    $join->on('products.id', '=', 'comment_metrics.product_id');
                })
                ->leftJoinSub($likeCounts, 'like_metrics', function ($join) {
                    $join->on('products.id', '=', 'like_metrics.product_id');
                })
                ->leftJoinSub($ratingStats, 'rating_metrics', function ($join) {
                    $join->on('products.id', '=', 'rating_metrics.product_id');
                })
                ->with([
                    'category:id,name,slug',
                    'media',
                    'campaigns',
                ])
                ->where('products.is_active', true)
                ->where('products.stock', '>', 0)
                ->orderByRaw("{$score} DESC")
                ->orderBy('products.id')
                ->limit(10)
                ->get()
                ->each(function ($product, $index) {
                    $product->rank = $index + 1;
                });
        });

        return PopularProductsHomepageComponentForMobileResource::collection($products);
    }
}
