<?php

namespace App\Support\Localization;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

/**
 * URLs of the current page in every locale, for routes whose URL does not
 * depend on translated slugs (home, listings, ...). Pages showing a record
 * use the record's own localizedUrls() instead.
 */
final class LocalizedRoute
{
    /**
     * Query parameters that identify a distinct page and therefore belong in
     * canonical and alternate URLs. Anything else (tracking, sorting) is dropped.
     */
    private const SIGNIFICANT_QUERY = ['page'];

    /**
     * @param  array<string, mixed>  $query  Extra query parameters per URL.
     * @return array<string, string> Locale code => absolute URL.
     */
    public static function alternates(?Request $request = null, array $query = []): array
    {
        $request ??= request();
        $route = $request->route();

        if (! $route instanceof Route || $route->getName() === null) {
            return self::homeAlternates();
        }

        $query = [...array_filter($request->only(self::SIGNIFICANT_QUERY), filled(...)), ...$query];
        $urls = [];

        foreach (Locale::cases() as $locale) {
            $urls[$locale->value] = route($route->getName(), [
                ...$route->parameters(),
                'locale' => $locale->value,
                ...$query,
            ]);
        }

        return $urls;
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, string>
     */
    public static function forRoute(string $name, array $parameters = []): array
    {
        $urls = [];

        foreach (Locale::cases() as $locale) {
            $urls[$locale->value] = route($name, [...$parameters, 'locale' => $locale->value]);
        }

        return $urls;
    }

    /**
     * @return array<string, string>
     */
    public static function homeAlternates(): array
    {
        return self::forRoute('site.home');
    }
}
