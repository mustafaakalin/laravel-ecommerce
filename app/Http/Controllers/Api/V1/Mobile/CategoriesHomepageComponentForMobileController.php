<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\CategoriesHomepageComponentForMobileResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class CategoriesHomepageComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $categories = Cache::remember('mobile_homepage_categories', self::CACHE_TTL, function () {
            return Category::query()
                ->select([
                    'id',
                    'name',
                    'slug',
                    'icon',
                    'parent_id',
                    'products_count',
                    'is_active',
                    'sort_order'
                ])
                ->whereNull('parent_id')
                ->withCount(['children', 'products as active_products_count' => function ($query) {
                    $query->where('is_active', true)
                          ->where('stock', '>', 0);
                }])
                ->with(['children' => function ($query) {
                    $query->select([
                        'id',
                        'name',
                        'slug',
                        'icon',
                        'parent_id',
                        'products_count',
                        'is_active',
                        'sort_order'
                    ])
                    ->where('is_active', true)
                    ->withCount(['products as active_products_count' => function ($query) {
                        $query->where('is_active', true)
                              ->where('stock', '>', 0);
                    }]);
                }])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        return CategoriesHomepageComponentForMobileResource::collection($categories);
    }
}
