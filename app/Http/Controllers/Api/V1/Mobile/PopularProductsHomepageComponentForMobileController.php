<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\PopularProductsHomepageComponentForMobileResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopularProductsHomepageComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $products = Cache::remember('mobile_popular_products', self::CACHE_TTL, function () {
            return Product::query()
                ->select([
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.price',
                    'products.stock',
                    'products.category_id',
                    'products.discount',
                    'products.is_active'
                ])
                ->selectRaw('(SELECT COUNT(*) FROM cart_items WHERE products.id = cart_items.product_id) as cart_items_count')
                ->selectRaw('(SELECT COUNT(*) FROM product_ratings WHERE products.id = product_ratings.product_id) as ratings_count')
                ->selectRaw('(SELECT COUNT(*) FROM comments WHERE products.id = comments.product_id) as comments_count')
                ->selectRaw('(SELECT COUNT(*) FROM likes WHERE products.id = likes.product_id) as likes_count')
                ->selectRaw('(SELECT COUNT(*) FROM order_items 
                    INNER JOIN orders ON order_items.order_id = orders.id 
                    WHERE products.id = order_items.product_id 
                    AND orders.status = "delivered") as order_items_count')
                ->selectRaw('ROUND(
                    (
                        COALESCE((SELECT COUNT(*) FROM cart_items WHERE products.id = cart_items.product_id), 0) * 0.15 + 
                        COALESCE((SELECT COUNT(*) FROM order_items 
                            INNER JOIN orders ON order_items.order_id = orders.id 
                            WHERE products.id = order_items.product_id 
                            AND orders.status = "delivered"), 0) * 0.35 + 
                        COALESCE((SELECT COUNT(*) FROM comments WHERE products.id = comments.product_id), 0) * 0.15 + 
                        COALESCE((SELECT COUNT(*) FROM likes WHERE products.id = likes.product_id), 0) * 0.15 + 
                        COALESCE((SELECT AVG(rating) FROM product_ratings WHERE product_ratings.product_id = products.id), 0) * 0.20
                    ), 1
                ) as popularity_score')
                ->selectRaw('ROW_NUMBER() OVER (ORDER BY popularity_score DESC) as rank')
                ->with([
                    'category:id,name,slug',
                    'media',
                    'ratings'
                ])
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->orderByDesc('popularity_score')
                ->limit(10)
                ->get();
        });
    
        return PopularProductsHomepageComponentForMobileResource::collection($products);
    }
}
