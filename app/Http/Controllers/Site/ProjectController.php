<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Site\Concerns\ResolvesLocalizedSlugs;
use App\Models\Project;
use App\Models\Sector;
use App\Support\Localization\Locale;
use App\Support\Seo\SeoData;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProjectController
{
    use ResolvesLocalizedSlugs;

    public function index(Request $request): View
    {
        $locale = Locale::current();
        $activeSector = Sector::findBySlug($request->string('sector')->toString(), $locale->value);

        $projects = Project::query()->published()
            ->with('sector')
            ->when($activeSector, fn ($query) => $query->whereBelongsTo($activeSector))
            ->latestFirst()
            ->paginate(9)
            ->withQueryString();

        // The sector filter uses each language's own slug.
        $alternates = [];
        foreach (Locale::cases() as $alternate) {
            $alternates[$alternate->value] = route('site.projects.index', array_filter([
                'locale' => $alternate->value,
                'sector' => $activeSector?->translate('slug', $alternate),
                'page' => $projects->currentPage() > 1 ? $projects->currentPage() : null,
            ]));
        }

        $title = $activeSector
            ? __('site.projects.sector_title', ['sector' => $activeSector->translate('name')])
            : __('site.projects.title');

        return view('site.projects.index', [
            'projects' => $projects,
            'sectors' => Sector::query()->ordered()->whereHas('projects', fn ($query) => $query->published())->get(),
            'activeSector' => $activeSector,
            'seo' => SeoData::make(title: $title, description: __('site.projects.intro'), alternates: $alternates),
        ]);
    }

    public function show(string $slug): View
    {
        $project = $this->findBySlugOrRedirect(Project::query()->published()->with('sector'), $slug);

        return view('site.projects.show', [
            'project' => $project,
            'disciplines' => $project->disciplines()->published()->ordered()->get(),
            'relatedProjects' => Project::query()->published()
                ->with('sector')
                ->whereKeyNot($project->getKey())
                ->where('sector_id', $project->sector_id)
                ->latestFirst()
                ->limit(3)
                ->get(),
            'seo' => SeoData::make(
                title: $project->seoTitle(),
                description: $project->seoDescription(),
                alternates: $project->localizedUrls(),
                image: $project->coverImageUrl(),
                type: 'article',
            ),
        ]);
    }
}
