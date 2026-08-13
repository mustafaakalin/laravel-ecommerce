<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\PopularCategoriesHomepageComponentForMobileResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopularCategoriesHomepageComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $categories = Cache::remember('mobile_popular_categories', self::CACHE_TTL, function () {
            return Category::query()
                ->select([
                    'categories.id',
                    'categories.name',
                    'categories.slug',
                    'categories.icon',
                    DB::raw('COUNT(DISTINCT order_items.id) as total_sales'),
                    DB::raw('COUNT(DISTINCT comments.id) as total_comments'),
                    DB::raw('ROUND(AVG(COALESCE(product_ratings.rating, 0)), 1) as average_rating'),
                    DB::raw('(
                        SELECT COUNT(p.id)
                        FROM products p
                        WHERE p.category_id = categories.id
                        AND p.is_active = true
                        AND p.stock > 0
                    ) as direct_products_count'),
                    DB::raw('(
                        SELECT COUNT(p2.id)
                        FROM categories c2
                        JOIN products p2 ON p2.category_id = c2.id
                        WHERE c2.parent_id = categories.id
                        AND p2.is_active = true
                        AND p2.stock > 0
                    ) as child_products_count'),
                    DB::raw('ROUND(
                        (
                            (COUNT(DISTINCT order_items.id) * 0.4) +
                            (COUNT(DISTINCT comments.id) * 0.3) +
                            (COALESCE(AVG(product_ratings.rating), 0) * 0.3)
                        ), 1
                    ) as popularity_score'),
                    DB::raw('ROW_NUMBER() OVER (ORDER BY (
                        (COUNT(DISTINCT order_items.id) * 0.4) +
                        (COUNT(DISTINCT comments.id) * 0.3) +
                        (COALESCE(AVG(product_ratings.rating), 0) * 0.3)
                    ) DESC) as rank')
                ])
                ->leftJoin('products', 'categories.id', '=', 'products.category_id')
                ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                ->leftJoin('orders', function ($join) {
                    $join->on('order_items.order_id', '=', 'orders.id')
                        ->where('orders.status', '=', 'delivered');
                })
                ->leftJoin('comments', 'products.id', '=', 'comments.product_id')
                ->leftJoin('product_ratings', 'products.id', '=', 'product_ratings.product_id')
                ->where('categories.is_active', true)
                ->groupBy('categories.id', 'categories.name', 'categories.slug', 'categories.icon')
                ->having(DB::raw('COUNT(DISTINCT order_items.id)'), '>', 0)
                ->orderByDesc('popularity_score')
                ->limit(10)
                ->get();
        });

        return PopularCategoriesHomepageComponentForMobileResource::collection($categories);
    }
}
