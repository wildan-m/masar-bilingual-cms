<?php

namespace App\Support\Localization;

/**
 * The languages the website is published in.
 *
 * English is the fallback locale: every translatable value is expected to
 * exist in English, and any other locale falls back to it when a
 * translation has not been written yet.
 */
enum Locale: string
{
    case English = 'en';
    case Arabic = 'ar';

    public static function fallback(): self
    {
        return self::English;
    }

    public static function current(): self
    {
        return self::tryFrom(app()->getLocale()) ?? self::fallback();
    }

    /**
     * Normalise a locale given as an enum, a code or nothing (current locale).
     */
    public static function resolve(self|string|null $locale = null): self
    {
        return match (true) {
            $locale instanceof self => $locale,
            $locale === null => self::current(),
            default => self::tryFrom($locale) ?? self::fallback(),
        };
    }

    /**
     * @return list<string>
     */
    public static function codes(): array
    {
        return array_map(fn (self $locale): string => $locale->value, self::cases());
    }

    /**
     * Regular expression fragment used to constrain the {locale} route segment.
     */
    public static function routePattern(): string
    {
        return implode('|', self::codes());
    }

    public function isFallback(): bool
    {
        return $this === self::fallback();
    }

    public function direction(): string
    {
        return $this === self::Arabic ? 'rtl' : 'ltr';
    }

    public function isRtl(): bool
    {
        return $this->direction() === 'rtl';
    }

    public function nativeName(): string
    {
        return match ($this) {
            self::English => 'English',
            self::Arabic => 'العربية',
        };
    }

    public function englishName(): string
    {
        return match ($this) {
            self::English => 'English',
            self::Arabic => 'Arabic',
        };
    }

    /**
     * Locale code used by the og:locale meta tags.
     */
    public function openGraphLocale(): string
    {
        return match ($this) {
            self::English => 'en_AE',
            self::Arabic => 'ar_AE',
        };
    }

    /**
     * @return list<self>
     */
    public function others(): array
    {
        return array_values(array_filter(self::cases(), fn (self $locale): bool => $locale !== $this));
    }
}
