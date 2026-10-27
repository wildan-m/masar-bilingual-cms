<?php

namespace Tests\Feature\Site;

use App\Models\Article;
use App\Models\Discipline;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function locales(): array
    {
        return ['English' => ['en'], 'Arabic' => ['ar']];
    }

    #[DataProvider('locales')]
    public function test_every_public_page_renders_in_both_languages(string $locale): void
    {
        $discipline = Discipline::factory()->create();
        $project = Project::factory()->featured()->create();
        $project->disciplines()->attach($discipline);
        $article = Article::factory()->create();
        $about = Page::factory()->about()->create();
        $contact = $this->contactPage();

        $urls = [
            route('site.home', ['locale' => $locale]),
            route('site.disciplines.index', ['locale' => $locale]),
            $discipline->url($locale),
            route('site.projects.index', ['locale' => $locale]),
            route('site.projects.index', ['locale' => $locale, 'sector' => $project->sector->translate('slug', $locale)]),
            $project->url($locale),
            route('site.news.index', ['locale' => $locale]),
            $article->url($locale),
            $about->url($locale),
            $contact->url($locale),
            route('site.rfp.thanks', ['locale' => $locale]),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_unpublished_content_is_not_found(): void
    {
        $project = Project::factory()->unpublished()->create();
        $article = Article::factory()->scheduled()->create();

        $this->get($project->url('en'))->assertNotFound();
        $this->get($article->url('en'))->assertNotFound();
    }

    public function test_unknown_pages_render_a_localized_not_found_page(): void
    {
        $this->get('/ar/no-such-page')
            ->assertNotFound()
            ->assertSee('الصفحة غير موجودة')
            ->assertSee('<html lang="ar" dir="rtl">', false);
    }
}
