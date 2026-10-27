<?php

namespace App\Support\Seo;

use App\Support\Localization\Locale;
use App\Support\Localization\LocalizedRoute;
use App\Support\Settings\SiteSettings;
use Illuminate\Support\Str;

/**
 * Everything the layout needs for the <head>: title, description, canonical
 * URL, hreflang alternates and Open Graph data.
 */
final readonly class SeoData
{
    /**
     * @param  array<string, string>  $alternates  Locale code => absolute URL of this page in that locale.
     */
    public function __construct(
        public string $title,
        public string $description,
        public array $alternates,
        public ?string $image = null,
        public string $type = 'website',
        public bool $indexable = true,
        public ?string $publishedTime = null,
    ) {}

    /**
     * @param  array<string, string>|null  $alternates  Defaults to the current route in every locale.
     */
    public static function make(
        string $title,
        ?string $description = null,
        ?array $alternates = null,
        ?string $image = null,
        string $type = 'website',
        ?string $publishedTime = null,
        bool $indexable = true,
    ): self {
        $description = filled($description)
            ? $description
            : app(SiteSettings::class)->text('default_meta_description')->get();

        return new self(
            title: $title,
            description: Str::limit(trim((string) preg_replace('/\s+/u', ' ', strip_tags($description))), 160),
            alternates: $alternates ?? LocalizedRoute::alternates(),
            image: $image,
            type: $type,
            indexable: $indexable,
            publishedTime: $publishedTime,
        );
    }

    /**
     * Metadata for pages without their own (error pages): not indexable.
     */
    public static function fallback(string $title = ''): self
    {
        return new self(
            title: $title,
            description: app(SiteSettings::class)->text('default_meta_description')->get(),
            alternates: LocalizedRoute::homeAlternates(),
            indexable: false,
        );
    }

    public function documentTitle(): string
    {
        $company = app(SiteSettings::class)->text('company_name')->get();

        return $this->title === '' || $this->title === $company ? $company : "{$this->title} | {$company}";
    }

    public function canonical(): string
    {
        return $this->alternates[Locale::current()->value] ?? url()->current();
    }

    /**
     * Target of hreflang="x-default": the version for visitors whose language
     * is not offered, i.e. the fallback locale.
     */
    public function defaultAlternate(): ?string
    {
        return $this->alternates[Locale::fallback()->value] ?? null;
    }

    public function imageUrl(): string
    {
        return $this->image ?? asset('images/og-default.png');
    }
}
