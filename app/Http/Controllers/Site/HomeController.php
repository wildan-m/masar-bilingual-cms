<?php

namespace App\Http\Controllers\Site;

use App\Models\Article;
use App\Models\Discipline;
use App\Models\Project;
use App\Support\Seo\SeoData;
use App\Support\Settings\SiteSettings;
use Illuminate\View\View;

final class HomeController
{
    public function __invoke(SiteSettings $settings): View
    {
        $foundedYear = (int) $settings->get('founded_year');

        return view('site.home', [
            'disciplines' => Discipline::query()->published()->ordered()->get(),
            'featuredProjects' => Project::query()->published()
                ->where('is_featured', true)
                ->with('sector')
                ->latestFirst()
                ->limit(3)
                ->get(),
            'latestArticles' => Article::query()->published()
                ->with('category')
                ->latest('published_at')
                ->limit(3)
                ->get(),
            'stats' => [
                'years' => $foundedYear > 0 ? now()->year - $foundedYear : null,
                'projects' => Project::query()->published()->count(),
                'disciplines' => Discipline::query()->published()->count(),
            ],
            'seo' => SeoData::make(
                title: $settings->text('company_name')->get(),
                description: $settings->text('default_meta_description')->get(),
            ),
        ]);
    }
}
