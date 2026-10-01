<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\PopularCategoriesHomepageComponentForMobileResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopularCategoriesHomepageComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600;

    public function index(): AnonymousResourceCollection
    {
        $categories = Cache::remember(\App\Support\CacheKeys::mobile('popular-categories'), self::CACHE_TTL, function () {
            $salesByCategory = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.status', 'delivered')
                ->select('products.category_id', DB::raw('COUNT(*) as total_sales'))
                ->groupBy('products.category_id');

            $commentsByCategory = DB::table('comments')
                ->join('products', 'comments.product_id', '=', 'products.id')
                ->select('products.category_id', DB::raw('COUNT(*) as total_comments'))
                ->groupBy('products.category_id');

            $ratingsByCategory = DB::table('product_ratings')
                ->join('products', 'product_ratings.product_id', '=', 'products.id')
                ->select(
                    'products.category_id',
                    DB::raw('AVG(product_ratings.rating) as average_rating')
                )
                ->groupBy('products.category_id');

            $directProducts = DB::table('products')
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->select('category_id', DB::raw('COUNT(*) as direct_products_count'))
                ->groupBy('category_id');

            $childProducts = DB::table('products')
                ->join('categories as child_categories', 'products.category_id', '=', 'child_categories.id')
                ->where('products.is_active', true)
                ->where('products.stock', '>', 0)
                ->whereNotNull('child_categories.parent_id')
                ->select('child_categories.parent_id', DB::raw('COUNT(*) as child_products_count'))
                ->groupBy('child_categories.parent_id');

            $score = '(
                COALESCE(sales_metrics.total_sales, 0) * 0.4 +
                COALESCE(comment_metrics.total_comments, 0) * 0.3 +
                COALESCE(rating_metrics.average_rating, 0) * 0.3
            )';

            return Category::query()
                ->select([
                    'categories.id',
                    'categories.name',
                    'categories.slug',
                    'categories.icon',
                ])
                ->selectRaw('COALESCE(sales_metrics.total_sales, 0) as total_sales')
                ->selectRaw('COALESCE(comment_metrics.total_comments, 0) as total_comments')
                ->selectRaw('COALESCE(rating_metrics.average_rating, 0) as average_rating')
                ->selectRaw('COALESCE(direct_metrics.direct_products_count, 0) as direct_products_count')
                ->selectRaw('COALESCE(child_metrics.child_products_count, 0) as child_products_count')
                ->selectRaw("ROUND({$score}, 1) as popularity_score")
                ->leftJoinSub($salesByCategory, 'sales_metrics', function ($join) {
                    $join->on('categories.id', '=', 'sales_metrics.category_id');
                })
                ->leftJoinSub($commentsByCategory, 'comment_metrics', function ($join) {
                    $join->on('categories.id', '=', 'comment_metrics.category_id');
                })
                ->leftJoinSub($ratingsByCategory, 'rating_metrics', function ($join) {
                    $join->on('categories.id', '=', 'rating_metrics.category_id');
                })
                ->leftJoinSub($directProducts, 'direct_metrics', function ($join) {
                    $join->on('categories.id', '=', 'direct_metrics.category_id');
                })
                ->leftJoinSub($childProducts, 'child_metrics', function ($join) {
                    $join->on('categories.id', '=', 'child_metrics.parent_id');
                })
                ->where('categories.is_active', true)
                ->whereNotNull('sales_metrics.total_sales')
                ->orderByRaw("{$score} DESC")
                ->orderBy('categories.id')
                ->limit(10)
                ->get()
                ->each(function ($category, $index) {
                    $category->rank = $index + 1;
                });
        });

        return PopularCategoriesHomepageComponentForMobileResource::collection($categories);
    }
}
