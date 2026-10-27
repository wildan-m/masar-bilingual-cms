<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Site\Concerns\ResolvesLocalizedSlugs;
use App\Models\Article;
use App\Models\NewsCategory;
use App\Support\Localization\Locale;
use App\Support\Seo\SeoData;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class NewsController
{
    use ResolvesLocalizedSlugs;

    public function index(Request $request): View
    {
        $locale = Locale::current();
        $activeCategory = NewsCategory::findBySlug($request->string('category')->toString(), $locale->value);

        $articles = Article::query()->published()
            ->with('category')
            ->when($activeCategory, fn ($query) => $query->whereBelongsTo($activeCategory, 'category'))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $alternates = [];
        foreach (Locale::cases() as $alternate) {
            $alternates[$alternate->value] = route('site.news.index', array_filter([
                'locale' => $alternate->value,
                'category' => $activeCategory?->translate('slug', $alternate),
                'page' => $articles->currentPage() > 1 ? $articles->currentPage() : null,
            ]));
        }

        $title = $activeCategory
            ? __('site.news.category_title', ['category' => $activeCategory->translate('name')])
            : __('site.news.title');

        return view('site.news.index', [
            'articles' => $articles,
            'categories' => NewsCategory::query()->ordered()->whereHas('articles', fn ($query) => $query->published())->get(),
            'activeCategory' => $activeCategory,
            'seo' => SeoData::make(title: $title, description: __('site.news.intro'), alternates: $alternates),
        ]);
    }

    public function show(string $slug): View
    {
        $article = $this->findBySlugOrRedirect(Article::query()->published()->with('category'), $slug);

        return view('site.news.show', [
            'article' => $article,
            'moreArticles' => Article::query()->published()
                ->with('category')
                ->whereKeyNot($article->getKey())
                ->latest('published_at')
                ->limit(3)
                ->get(),
            'seo' => SeoData::make(
                title: $article->seoTitle(),
                description: $article->seoDescription(),
                alternates: $article->localizedUrls(),
                image: $article->coverImageUrl(),
                type: 'article',
                publishedTime: $article->published_at?->toAtomString(),
            ),
        ]);
    }
}
