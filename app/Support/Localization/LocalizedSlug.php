<?php

namespace App\Support\Localization;

use Illuminate\Support\Str;

/**
 * Builds URL slugs per locale.
 *
 * English slugs are transliterated to ASCII. Arabic slugs keep the Arabic
 * script so that shared links stay readable for Arabic-speaking visitors;
 * browsers display them decoded and Laravel percent-encodes them in URLs.
 */
final class LocalizedSlug
{
    /**
     * Letters and digits of any script, separated by single hyphens.
     */
    public const PATTERN = '/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u';

    public static function make(?string $text, Locale|string $locale): string
    {
        $text = trim((string) $text);

        if ($text === '') {
            return '';
        }

        if (Locale::resolve($locale) === Locale::Arabic) {
            // Drop tatweel (kashida) which is a letter modifier, not a letter.
            return Str::slug(str_replace("\u{0640}", '', $text), '-', null);
        }

        return Str::slug($text, '-', Locale::resolve($locale)->value);
    }

    public static function isValid(string $slug): bool
    {
        return preg_match(self::PATTERN, $slug) === 1;
    }
}
