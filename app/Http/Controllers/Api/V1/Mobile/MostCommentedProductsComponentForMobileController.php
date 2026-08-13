<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\MostCommentedProductsComponentForMobileResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class MostCommentedProductsComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $products = Cache::remember('mobile_most_commented_products', self::CACHE_TTL, function () {
            return Product::query()
                ->select([
                    'id',
                    'name',
                    'slug',
                    'price',
                    'stock',
                    'category_id',
                    'is_active',
                    'discount'
                ])
                ->withCount('comments')
                ->with([
                    'category:id,name,slug',
                    'media',
                    'ratings'
                ])
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->orderByDesc('comments_count')
                ->take(10)
                ->get();
        });

        return MostCommentedProductsComponentForMobileResource::collection($products);
    }
}
