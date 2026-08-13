<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Models\Cart;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Mobile\StatisticsForHomepageForProductMobileResource;
use App\Http\Resources\Mobile\StatisticsForHomepageCategoryForMobileResource;

class StatisticsForHomepageForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_product_count' => $this->getTotalProductCount(),
                'total_category_count' => $this->getTotalCategoryCount(),
                'total_brand_count' => $this->getTotalBrandCount(),
                'latest_3_campaign' => $this->getLatest3Campaign(),
                'featured_products' => $this->getFeaturedProducts(),
                'new_products' => $this->getNewProducts(),
                'categories' => $this->getCategories(),
                'campaigns' => $this->getCampaigns(),
                'brands' => $this->getBrands(),
                'testimonials' => $this->getTestimonials(),
            ]
        ]);
    }

    private function getFeaturedProducts()
    {
        return Cache::remember('mobile_featured_products', self::CACHE_TTL, function () {
            return StatisticsForHomepageForProductMobileResource::collection(
                Product::with(['category', 'images', 'ratings'])
                    ->active()
                    ->featured()
                    ->inStock()
                    ->latest()
                    ->take(4)
                    ->get()
            );
        });
    }

    private function getNewProducts()
    {
        return Cache::remember('mobile_new_products', self::CACHE_TTL, function () {
            return StatisticsForHomepageForProductMobileResource::collection(
                Product::with(['category', 'media', 'ratings'])
                    ->active()
                    ->new()
                    ->inStock()
                    ->latest()
                    ->take(4)
                    ->get()
            );
        });
    }

    private function getCategories()
    {
        return Cache::remember('mobile_categories', self::CACHE_TTL, function () {
            return StatisticsForHomepageCategoryForMobileResource::collection(
                Category::whereNull('parent_id')
                    ->where('is_active', true)
                    ->withCount(['products' => function ($query) {
                        $query->active();
                    }])
                    ->latest()
                    ->get()
            );
        });
    }

    private function getCampaigns()
    {
        return Cache::remember('mobile_campaigns', self::CACHE_TTL, function () {
            return Campaign::with('products')
                ->where('is_active', true)
                ->whereDate('end_date', '>=', now())
                ->orderBy('start_date', 'desc')
                ->get()
                ->map(function ($campaign) {
                    return [
                        'id' => $campaign->id,
                        'title' => $campaign->title,
                        'description' => $campaign->description,
                        'discount_rate' => $campaign->discount_rate,
                        'start_date' => $campaign->start_date,
                        'end_date' => $campaign->end_date,
                        // 'image' => $campaign->getFirstMediaUrl('image'),
                    ];
                });
        });
    }

    private function getBrands()
    {
        return Cache::remember('mobile_brands', self::CACHE_TTL, function () {
            return Brand::where('is_active', true)
                ->withCount('products')
                ->latest()
                ->get()
                ->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name,
                        'slug' => $brand->slug,
                        // 'logo' => $brand->getFirstMediaUrl('logo'), // for production or more defaults
                        'logo' => $brand->logo,
                        'products_count' => $brand->products_count,
                    ];
                });
        });
    }

    private function getTestimonials()
    {
        return Cache::remember('mobile_testimonials', self::CACHE_TTL, function () {
            return Testimonial::where('is_active', true)
                ->latest()
                ->get()
                ->map(function ($testimonial) {
                    return [
                        'id' => $testimonial->id,
                        'name' => $testimonial->name,
                        'comment' => $testimonial->comment,
                        'rating' => $testimonial->rating,
                        // 'avatar' => $testimonial->getFirstMediaUrl('avatar'),
                        'avatar' => $testimonial->avatar,
                    ];
                });
        });
    }

    private function getTotalProductCount()
    {
        return Cache::remember('mobile_total_product_count', self::CACHE_TTL, function () {
            return Product::count() - 1;
        });
    }

    private function getTotalCategoryCount()
    {
        return Cache::remember('mobile_total_category_count', self::CACHE_TTL, function () {
            return Category::count() - 1;
        });
    }

    private function getTotalBrandCount()
    {
        return Cache::remember('mobile_total_brand_count', self::CACHE_TTL, function () {
            return Brand::count() - 1;
        });
    }

    private function getLatest3Campaign()
    {
        return Cache::remember('mobile_latest_3_campaign', self::CACHE_TTL, function () {
            return Campaign::query()
                ->select([
                    'id',
                    'name',
                    'description',
                    'discount_type',
                    'discount_value',
                    'start_date',
                    'end_date',
                    'is_active'
                ])
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($campaign) {
                    return [
                        'id' => $campaign->id,
                        'name' => $campaign->name,
                        'description' => $campaign->description,
                        'discount_type' => $campaign->discount_type,
                        'discount_value' => $campaign->discount_value,
                        'start_date' => $campaign->start_date->format('Y-m-d'),
                        'end_date' => $campaign->end_date->format('Y-m-d'),
                        'is_active' => $campaign->is_active,
                        'status' => now()->between($campaign->start_date, $campaign->end_date) && $campaign->is_active ? 'active' : 'inactive'
                    ];
                });
        });
    }


}
