<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\FeaturedProductsComponentForMobileResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class FeaturedProductsComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $products = Cache::remember('mobile_featured_products', self::CACHE_TTL, function () {
            return Product::query()
                ->select([
                    'id',
                    'name',
                    'slug',
                    'price',
                    'stock',
                    'category_id',
                    'is_active',
                    'is_new',
                    'is_digital',
                    'is_free_shipping',
                    'discount'
                ])
                ->with([
                    'category:id,name,slug',
                    'media',
                    'ratings'
                ])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->where('stock', '>', 0)
                ->orderBy('updated_at', 'desc')
                ->take(8)
                ->get();
        });

        return FeaturedProductsComponentForMobileResource::collection($products);
    }
}
