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
            static fn () => [
                'productCount' => Product::query()
                    ->where('is_active', true)
                    ->count(),

                'featuredProducts' => Product::query()
                    ->with(['category', 'images'])
                    ->active()
                    ->featured()
                    ->inStock()
                    ->latest()
                    ->limit(4)
                    ->get(),

                'newProducts' => Product::query()
                    ->with(['category', 'images'])
                    ->active()
                    ->new()
                    ->inStock()
                    ->latest()
                    ->limit(4)
                    ->get(),

                'campaigns' => Campaign::query()
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
                    ->where('is_active', true)
                    ->withCount('products')
                    ->latest()
                    ->get(),

                'testimonials' => Testimonial::query()
                    ->where('is_active', true)
                    ->get(),
            ]
        );

        return view('home', $home);
    }
}
