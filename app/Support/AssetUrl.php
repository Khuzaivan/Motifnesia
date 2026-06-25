<?php

namespace App\Support;

class AssetUrl
{
    public static function product(?string $path): string
    {
        $path = trim((string) $path);
        $fallback = 'placeholder_image.jpg';

        if ($path === '') {
            return asset($fallback);
        }

        if (preg_match('/^(https?:)?\/\//i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if (
            str_starts_with($normalized, 'assets/')
            || str_starts_with($normalized, 'images/')
            || str_starts_with($normalized, 'storage/')
            || str_starts_with($normalized, 'build/')
        ) {
            return asset($normalized);
        }

        if (str_starts_with($normalized, 'photoProduct/')) {
            return asset('assets/' . $normalized);
        }

        if (file_exists(public_path('assets/photoProduct/' . $normalized))) {
            return asset('assets/photoProduct/' . $normalized);
        }

        if (file_exists(public_path('images/' . $normalized))) {
            return asset('images/' . $normalized);
        }

        return asset($fallback);
    }

    public static function profile(?string $path, ?string $name = null): string
    {
        if (filled($path)) {
            return self::publicAsset($path, 'images/default.jpg', 'images');
        }

        $displayName = $name ?: 'User';

        return 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=c9a84c&color=111&size=200';
    }

    public static function publicAsset(?string $path, string $fallback, string $barePrefix = 'images'): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return asset($fallback);
        }

        if (preg_match('/^(https?:)?\/\//i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if (
            str_starts_with($normalized, 'assets/')
            || str_starts_with($normalized, 'images/')
            || str_starts_with($normalized, 'storage/')
            || str_starts_with($normalized, 'build/')
        ) {
            return asset($normalized);
        }

        return asset(trim($barePrefix, '/') . '/' . $normalized);
    }
}
