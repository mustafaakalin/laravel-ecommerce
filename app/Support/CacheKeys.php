<?php

namespace App\Support;

final class CacheKeys
{
    public const VERSION = 'v1';

    public static function mobile(string $resource): string
    {
        return self::VERSION . ':mobile:' . $resource;
    }

    public static function catalog(string $resource): string
    {
        return self::VERSION . ':catalog:' . $resource;
    }

    public static function siteSettings(): string
    {
        return self::VERSION . ':site-settings';
    }

    public static function homepage(string $resource): string
    {
        return self::VERSION . ':homepage:' . $resource;
    }

    public static function rankings(string $resource): string
    {
        return self::VERSION . ':ranking:' . $resource;
    }

    public static function product(int|string $id): string
    {
        return self::VERSION . ':product:' . $id;
    }
}
