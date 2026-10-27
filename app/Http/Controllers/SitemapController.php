<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Discipline;
use App\Models\Page;
use App\Models\Project;
use App\Support\Localization\LocalizedRoute;
use App\Support\Seo\SitemapBuilder;
use Illuminate\Http\Response;

final class SitemapController
{
    private const LISTING_ROUTES = [
        'site.home',
        'site.disciplines.index',
        'site.projects.index',
        'site.news.index',
    ];

    public function __invoke(): Response
    {
        $sitemap = new SitemapBuilder;

        foreach (self::LISTING_ROUTES as $route) {
            $sitemap->add(LocalizedRoute::forRoute($route));
        }

        foreach ([Page::class, Discipline::class, Project::class, Article::class] as $model) {
            $model::query()->published()->orderBy('id')->each(
                fn ($record) => $sitemap->add($record->localizedUrls(), $record->updated_at),
            );
        }

        return response($sitemap->toXml(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
