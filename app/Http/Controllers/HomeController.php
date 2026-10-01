<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use App\Models\Testimonial;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $home = Cache::flexible(
            CacheKeys::homepage('catalog'),
            [30, 120],
            static function () {
                return [
                    // The homepage only needs the count, not every active product row.
                    'productCount' => Product::query()->where('is_active', true)->count(),

                    'featuredProducts' => Product::with(['category', 'images'])
                        ->active()
                        ->featured()
                        ->inStock()
                        ->latest()
                        ->take(4)
                        ->get(),

                    'newProducts' => Product::with(['category', 'images'])
                        ->active()
                        ->new()
                        ->inStock()
                        ->latest()
                        ->take(4)
                        ->get(),

                    'campaigns' => Campaign::with('products')
                        ->where('is_active', true)
                        ->latest('start_date')
                        ->get(),

                    'categories' => Category::query()
                        ->whereNull('parent_id')
                        ->where('is_active', true)
                        ->activeProductsCount()
                        ->latest()
                        ->get(),

                    'brands' => Brand::query()
                        ->with('products')
                        ->where('is_active', true)
                        ->latest()
                        ->get(),

                    'testimonials' => Testimonial::query()
                        ->where('is_active', true)
                        ->get(),
                ];
            }
        );

        return view('home', [
            'products' => Product::query()
                ->where('is_active', true)
                ->select(['id'])
                ->limit(1)
                ->get(),
            'productCount' => $home['productCount'],
            'featuredProducts' => $home['featuredProducts'],
            'newProducts' => $home['newProducts'],
            'categories' => $home['categories'],
            'campaigns' => $home['campaigns'],
            'brands' => $home['brands'],
            'testimonials' => $home['testimonials'],
        ]);
    }
}
