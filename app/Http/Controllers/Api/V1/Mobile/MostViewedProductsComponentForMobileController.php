<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\MostViewedProductsComponentForMobileResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class MostViewedProductsComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $products = Cache::remember('mobile_most_viewed_products', self::CACHE_TTL, function () {
            return Product::query()
                ->select([
                    'id', 
                    'name', 
                    'slug', 
                    'price', 
                    'stock',
                    'category_id',
                    'view_count',
                    'is_active'
                ])
                ->with(['category:id,name,slug', 'media', 'ratings'])
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->orderBy('view_count', 'desc')
                ->take(8)
                ->get();
        });

        return MostViewedProductsComponentForMobileResource::collection($products);
    }
}
