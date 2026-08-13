<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

trait ApiControllerTrait
{
    protected function cachedResponse(string $key, \Closure $callback, int $ttl = 3600): JsonResponse
    {
        return response()->json(
            Cache::remember($key, $ttl, $callback),
            Response::HTTP_OK
        );
    }

    protected function successResponse($data, string $message = null, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse(string $message, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }

    protected function getDefaultImage(string $type): string
    {
        $settings = Cache::remember('site_settings', 3600, function () {
            return \App\Models\SiteSetting::first();
        });

        $defaultImages = [
            'brand' => $settings->default_brand_image ?? 'assets/images/defaults/brand.png',
            'category' => $settings->default_category_image ?? 'assets/images/defaults/category.png',
            'product' => $settings->default_product_image ?? 'assets/images/defaults/product.png',
            'testimonial' => $settings->default_testimonial_image ?? 'assets/images/defaults/testimonial.png',
            'user' => $settings->default_user_avatar ?? 'assets/images/defaults/user.png',
            'slider' => $settings->default_slider_image ?? 'assets/images/defaults/slider.png',
        ];

        return $defaultImages[$type] ?? $defaultImages['product'];
    }
}