<?php

namespace Tests\Feature\Seo;

use App\Models\Article;
use App\Models\Page;
use App\Models\Project;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    private function sitemap(): DOMXPath
    {
        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $document = new DOMDocument;
        $document->loadXML($response->getContent());

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');

        return $xpath;
    }

    public function test_every_url_lists_its_translations_as_hreflang_alternates(): void
    {
        $project = Project::factory()->create();
        $about = Page::factory()->about()->create();

        $xpath = $this->sitemap();

        foreach ([$project, $about] as $record) {
            foreach (['en', 'ar'] as $locale) {
                $url = $record->url($locale);
                $entry = $xpath->query("//s:url[s:loc='{$url}']");

                $this->assertSame(1, $entry->length, "Missing sitemap entry for {$url}");

                $alternates = [];
                foreach ($xpath->query('xhtml:link[@rel="alternate"]', $entry->item(0)) as $link) {
                    $alternates[$link->getAttribute('hreflang')] = $link->getAttribute('href');
                }

                $this->assertSame([
                    'en' => $record->url('en'),
                    'ar' => $record->url('ar'),
                    'x-default' => $record->url('en'),
                ], $alternates);
            }
        }

        $this->assertSame(1, $xpath->query("//s:url[s:loc='".url('/ar/projects')."']/xhtml:link[@hreflang='en']")->length);
    }

    public function test_unpublished_content_is_left_out(): void
    {
        $draft = Project::factory()->unpublished()->create();
        $scheduled = Article::factory()->scheduled()->create();

        $xpath = $this->sitemap();

        $this->assertSame(0, $xpath->query("//s:url[s:loc='{$draft->url('en')}']")->length);
        $this->assertSame(0, $xpath->query("//s:url[s:loc='{$scheduled->url('en')}']")->length);
    }

    public function test_robots_txt_points_crawlers_to_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_project_pages_carry_open_graph_metadata_in_the_page_language(): void
    {
        $project = Project::factory()->create([
            'title' => ['en' => 'Corniche Residences Tower', 'ar' => 'برج كورنيش ريزيدنسز'],
            'meta_description' => ['en' => 'A 42-storey residential tower.', 'ar' => 'برج سكني من 42 طابقًا.'],
        ]);

        $this->get($project->url('ar'))
            ->assertSee('<title>برج كورنيش ريزيدنسز | ', false)
            ->assertSee('<meta name="description" content="برج سكني من 42 طابقًا.">', false)
            ->assertSee('<meta property="og:title" content="برج كورنيش ريزيدنسز">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:locale:alternate" content="en_AE">', false);
    }

    public function test_cms_rich_text_is_sanitised_before_it_is_published(): void
    {
        $page = Page::factory()->create([
            'body' => ['en' => '<p onclick="steal()">Hello</p><script>alert(1)</script><a href="javascript:alert(1)">x</a>'],
        ]);

        $this->get($page->url('en'))
            ->assertOk()
            ->assertSee('Hello')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('onclick', false)
            ->assertDontSee('javascript:alert', false);
    }
}
