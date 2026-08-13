<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\BrandsHomepageComponentForMobileResource;
use App\Models\Brand;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class BrandsHomepageComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $brands = Cache::remember('mobile_homepage_brands', self::CACHE_TTL, function () {
            return Brand::query()
                ->select([
                    'id',
                    'name',
                    'slug',
                    // 'sort_order',
                    'is_active'
                ])
                ->withCount(['products as active_products_count' => function ($query) {
                    $query->where('is_active', true)
                          ->where('stock', '>', 0);
                }])
                ->with('media')
                ->where('is_active', true)
                ->having('active_products_count', '>', 0)
                // ->orderBy('sort_order')
                ->orderBy('name')
                ->take(12)
                ->get();
        });

        return BrandsHomepageComponentForMobileResource::collection($brands);
    }
}
