<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Cache;

final class SiteSettingService
{
    public function get(): ?SiteSetting
    {
        return Cache::remember(
            CacheKeys::siteSettings(),
            3600,
            static fn () => SiteSetting::query()->first()
        );
    }

    public function forget(): void
    {
        Cache::forget(CacheKeys::siteSettings());
    }
}
