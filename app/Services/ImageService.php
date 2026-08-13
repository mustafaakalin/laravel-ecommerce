<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ImageService
{
    private const DEFAULT_IMAGES = [
        'brand' => 'assets/images/defaults/brand.png',
        'category' => 'assets/images/defaults/category.png',
        'etbis_qr' => 'assets/images/defaults/etbis_qr.png',
        'favicon' => 'assets/images/defaults/favicon.png',
        'logo' => 'assets/images/defaults/logo.png',
        'product' => 'assets/images/defaults/product.png',
        'slider' => 'assets/images/defaults/slider.png',
        'testimonial' => 'assets/images/defaults/testimonial.png',
        'user' => 'assets/images/defaults/user.png',
    ];

    public function getDefaultImage(string $type): string
    {
        if (!isset(self::DEFAULT_IMAGES[$type])) {
            return self::DEFAULT_IMAGES['product'];
        }

        $path = self::DEFAULT_IMAGES[$type];
        
        // Check if custom default exists in settings
        $settings = Cache::remember('site_settings', 3600, function () {
            return \App\Models\SiteSetting::first();
        });

        $settingKey = "default_{$type}_image";
        if ($settings && $settings->$settingKey) {
            $customPath = $settings->$settingKey;
            if (Storage::disk('public')->exists($customPath)) {
                return $customPath;
            }
        }

        return $path;
    }

    public function ensureDefaultImages(): void
    {
        foreach (self::DEFAULT_IMAGES as $type => $path) {
            $this->copyDefaultImage($type);
        }
    }

    private function copyDefaultImage(string $type): void
    {
        $sourcePath = public_path(self::DEFAULT_IMAGES[$type]);
        $targetPath = storage_path('app/public/' . self::DEFAULT_IMAGES[$type]);

        if (!file_exists($sourcePath)) {
            \Log::warning("Default image not found: {$sourcePath}");
            return;
        }

        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!file_exists($targetPath)) {
            copy($sourcePath, $targetPath);
        }
    }
}