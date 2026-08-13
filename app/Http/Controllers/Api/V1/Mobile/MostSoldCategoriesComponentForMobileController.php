<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\MostSoldCategoriesComponentForMobileResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MostSoldCategoriesComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $categories = Cache::remember('mobile_most_sold_categories', self::CACHE_TTL, function () {
            return Category::select([
                    'categories.id',
                    'categories.name',
                    'categories.icon',
                    'categories.products_count',
                    DB::raw('COUNT(order_items.id) as total_sales'),
                    DB::raw('ROW_NUMBER() OVER (ORDER BY COUNT(order_items.id) DESC) as rank')
                ])
                ->activeProductsCount()
                // ->with('media')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->join('order_items', 'products.id', '=', 'order_items.product_id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'delivered')
                ->where('categories.is_active', true)
                ->groupBy([
                    'categories.id',
                    'categories.name',
                    'categories.icon',
                    'categories.products_count'
                ])
                ->orderByDesc('total_sales')
                ->limit(10)
                ->get();
        });

        return MostSoldCategoriesComponentForMobileResource::collection($categories);
    }
}
