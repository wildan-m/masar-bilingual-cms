<?php

namespace App\Support\Site;

use App\Models\Page;
use Illuminate\Support\Collection;

/**
 * Links shared by the header and footer. Published pages are loaded once
 * per render of the layout.
 */
final class Navigation
{
    /**
     * @var Collection<int, Page>
     */
    private Collection $pages;

    public function __construct()
    {
        $this->pages = Page::query()->published()->orderBy('id')->get();
    }

    public function pageUrl(string $key): ?string
    {
        return $this->pages->firstWhere('key', $key)?->url();
    }

    /**
     * @return list<array{label: string, url: string, active: bool}>
     */
    public function mainItems(): array
    {
        $items = [
            $this->routeItem(__('site.nav.disciplines'), 'site.disciplines.index', 'site.disciplines.*'),
            $this->routeItem(__('site.nav.projects'), 'site.projects.index', 'site.projects.*'),
            $this->routeItem(__('site.nav.news'), 'site.news.index', 'site.news.*'),
            $this->pageItem(__('site.nav.about'), Page::ABOUT),
            $this->pageItem(__('site.nav.contact'), Page::CONTACT),
        ];

        return array_values(array_filter($items));
    }

    /**
     * Published pages other than the ones in the main navigation.
     *
     * @return Collection<int, Page>
     */
    public function secondaryPages(): Collection
    {
        return $this->pages
            ->reject(fn (Page $page): bool => in_array($page->key, [Page::ABOUT, Page::CONTACT], true))
            ->values();
    }

    /**
     * @return array{label: string, url: string, active: bool}
     */
    private function routeItem(string $label, string $route, string $pattern): array
    {
        return ['label' => $label, 'url' => route($route), 'active' => request()->routeIs($pattern)];
    }

    /**
     * @return array{label: string, url: string, active: bool}|null
     */
    private function pageItem(string $label, string $key): ?array
    {
        $page = $this->pages->firstWhere('key', $key);

        if ($page === null) {
            return null;
        }

        return [
            'label' => $label,
            'url' => $page->url(),
            'active' => request()->routeIs('site.pages.show') && request()->route('slug') === $page->slugFor(),
        ];
    }
}
