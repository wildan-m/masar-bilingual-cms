<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Site\Concerns\ResolvesLocalizedSlugs;
use App\Models\Discipline;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

final class DisciplineController
{
    use ResolvesLocalizedSlugs;

    public function index(): View
    {
        return view('site.disciplines.index', [
            'disciplines' => Discipline::query()->published()->ordered()->get(),
            'seo' => SeoData::make(
                title: __('site.disciplines.title'),
                description: __('site.disciplines.intro'),
            ),
        ]);
    }

    public function show(string $slug): View
    {
        $discipline = $this->findBySlugOrRedirect(Discipline::query()->published(), $slug);

        return view('site.disciplines.show', [
            'discipline' => $discipline,
            'projects' => $discipline->projects()
                ->published()
                ->with('sector')
                ->latestFirst()
                ->limit(6)
                ->get(),
            'otherDisciplines' => Discipline::query()->published()->ordered()->whereKeyNot($discipline->getKey())->get(),
            'seo' => SeoData::make(
                title: $discipline->seoTitle(),
                description: $discipline->seoDescription(),
                alternates: $discipline->localizedUrls(),
            ),
        ]);
    }
}
