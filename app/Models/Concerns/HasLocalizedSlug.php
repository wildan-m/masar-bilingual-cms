<?php

namespace App\Models\Concerns;

use App\Support\Localization\Locale;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Public URLs built from a localized `slug` attribute.
 *
 * Requires HasLocalizedAttributes and a `slug` entry in `$localized`.
 * A record without an Arabic slug is still reachable in Arabic through its
 * English slug, so the language switcher never leads to a dead page.
 */
trait HasLocalizedSlug
{
    /**
     * Name of the public route that shows a single record.
     */
    abstract public static function publicRouteName(): string;

    public function slugFor(Locale|string|null $locale = null): string
    {
        return $this->translate('slug', $locale);
    }

    public function url(Locale|string|null $locale = null): string
    {
        $locale = Locale::resolve($locale);

        return route(static::publicRouteName(), [
            'locale' => $locale->value,
            'slug' => $this->slugFor($locale),
        ]);
    }

    /**
     * @return array<string, string> Locale code => absolute URL.
     */
    public function localizedUrls(): array
    {
        $urls = [];

        foreach (Locale::cases() as $locale) {
            $urls[$locale->value] = $this->url($locale);
        }

        return $urls;
    }

    /**
     * Match the slug of a locale, or the fallback slug of records that have
     * not been given a slug in that locale yet.
     */
    #[Scope]
    protected function whereLocalizedSlug(Builder $query, string $slug, Locale|string $locale): void
    {
        $code = Locale::resolve($locale)->value;
        $fallback = Locale::fallback()->value;

        $query->where(function (Builder $query) use ($slug, $code, $fallback): void {
            $query->where("slug->{$code}", $slug);

            if ($code !== $fallback) {
                $query->orWhere(fn (Builder $query) => $query
                    ->whereNull("slug->{$code}")
                    ->where("slug->{$fallback}", $slug));
            }
        });
    }

    /**
     * Match the slug in any locale (used to redirect mixed-language URLs).
     */
    #[Scope]
    protected function whereAnySlug(Builder $query, string $slug): void
    {
        $query->where(function (Builder $query) use ($slug): void {
            foreach (Locale::cases() as $locale) {
                $query->orWhere("slug->{$locale->value}", $slug);
            }
        });
    }
}
