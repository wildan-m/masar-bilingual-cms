<?php

namespace App\Models\Concerns;

use App\Support\Localization\Locale;
use Illuminate\Support\Str;

/**
 * Per-locale meta title and description with sensible fallbacks.
 *
 * Requires HasLocalizedAttributes with `meta_title` and `meta_description`.
 * A missing Arabic meta title falls back to the Arabic title before falling
 * back to English, so search results stay in the visitor's language.
 */
trait HasSeoFields
{
    abstract protected function seoTitleAttribute(): string;

    abstract protected function seoSummaryAttribute(): string;

    public function seoTitle(Locale|string|null $locale = null): string
    {
        $locale = Locale::resolve($locale);

        return $this->localizedText('meta_title')->in($locale)
            ?? $this->localizedText($this->seoTitleAttribute())->in($locale)
            ?? $this->localizedText('meta_title')->in(Locale::fallback())
            ?? $this->translate($this->seoTitleAttribute(), $locale);
    }

    public function seoDescription(Locale|string|null $locale = null): string
    {
        $locale = Locale::resolve($locale);

        $description = $this->localizedText('meta_description')->in($locale)
            ?? $this->localizedText($this->seoSummaryAttribute())->in($locale)
            ?? $this->localizedText('meta_description')->in(Locale::fallback())
            ?? $this->translate($this->seoSummaryAttribute(), $locale);

        return Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags($description))), 160);
    }
}
