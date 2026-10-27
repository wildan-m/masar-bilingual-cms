<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageTemplate;
use App\Http\Controllers\Site\Concerns\ResolvesLocalizedSlugs;
use App\Models\Page;
use App\Models\Sector;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

/**
 * CMS pages, matched by their localized slug (/en/about, /ar/من-نحن).
 */
final class PageController
{
    use ResolvesLocalizedSlugs;

    public function __invoke(string $slug): View
    {
        $page = $this->findBySlugOrRedirect(Page::query()->published(), $slug);

        $seo = SeoData::make(
            title: $page->seoTitle(),
            description: $page->seoDescription(),
            alternates: $page->localizedUrls(),
        );

        return match ($page->template) {
            PageTemplate::Contact => view('site.pages.contact', [
                'page' => $page,
                'sectors' => Sector::query()->ordered()->get(),
                'seo' => $seo,
            ]),
            default => view('site.pages.show', ['page' => $page, 'seo' => $seo]),
        };
    }
}
