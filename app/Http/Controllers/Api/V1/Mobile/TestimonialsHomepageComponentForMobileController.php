<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\TestimonialsHomepageComponentForMobileResource;
use App\Models\Testimonial;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class TestimonialsHomepageComponentForMobileController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour cache

    public function index(): AnonymousResourceCollection
    {
        $testimonials = Cache::remember('mobile_homepage_testimonials', self::CACHE_TTL, function () {
            return Testimonial::query()
                ->select([
                    'id',
                    'author',
                    'position',
                    'content',
                    'rating',
                    'created_at'
                ])
                ->with('media')
                ->active()
                ->orderBy('rating', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        });

        return TestimonialsHomepageComponentForMobileResource::collection($testimonials);
    }
}
