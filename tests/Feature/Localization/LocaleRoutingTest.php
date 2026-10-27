<?php

namespace Tests\Feature\Localization;

use App\Models\Project;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_root_url_redirects_arabic_browsers_to_the_arabic_site(): void
    {
        $this->get('/', ['Accept-Language' => 'ar-AE,ar;q=0.9,en;q=0.8'])
            ->assertRedirect('/ar')
            ->assertHeader('Vary', 'Accept-Language, Cookie');
    }

    public function test_the_root_url_redirects_to_english_by_default(): void
    {
        $this->get('/')->assertRedirect('/en');
        $this->get('/', ['Accept-Language' => 'fr-FR,fr;q=0.9'])->assertRedirect('/en');
        $this->get('/', ['Accept-Language' => 'en-GB,en;q=0.9,ar;q=0.8'])->assertRedirect('/en');
    }

    public function test_the_root_url_remembers_the_language_the_visitor_chose(): void
    {
        $this->withCookie('locale', 'ar')
            ->get('/', ['Accept-Language' => 'en-US'])
            ->assertRedirect('/ar');
    }

    public function test_visiting_a_language_remembers_it_for_the_root_url(): void
    {
        $this->get('/ar')->assertCookie('locale', 'ar');
    }

    public function test_arabic_pages_are_rendered_right_to_left(): void
    {
        $this->get('/ar')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false);
    }

    public function test_english_pages_are_rendered_left_to_right(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false);
    }

    public function test_unsupported_language_prefixes_are_not_found(): void
    {
        $this->get('/fr')->assertNotFound();
        $this->get('/fr/projects')->assertNotFound();
    }

    public function test_the_language_switcher_links_to_the_same_record_in_the_other_language(): void
    {
        $project = Project::factory()->create([
            'title' => ['en' => 'Yas Waterfront Pedestrian Bridge', 'ar' => 'جسر المشاة على واجهة ياس'],
            'slug' => ['en' => 'yas-waterfront-pedestrian-bridge', 'ar' => 'جسر-المشاة-على-واجهة-ياس'],
        ]);

        $arabicUrl = $project->url('ar');
        $this->assertStringContainsString(rawurlencode('جسر-المشاة-على-واجهة-ياس'), $arabicUrl);

        $this->get($project->url('en'))
            ->assertOk()
            ->assertSee('href="'.$arabicUrl.'"', false)
            ->assertSee('hreflang="ar"', false);

        $this->get($arabicUrl)
            ->assertOk()
            ->assertSee('جسر المشاة على واجهة ياس')
            ->assertSee('href="'.$project->url('en').'"', false);
    }

    public function test_a_slug_from_the_other_language_redirects_to_the_canonical_url(): void
    {
        $project = Project::factory()->create([
            'slug' => ['en' => 'al-falah-interchange-upgrade', 'ar' => 'تطوير-تقاطع-الفلاح'],
        ]);

        $this->get('/ar/projects/al-falah-interchange-upgrade')
            ->assertStatus(301)
            ->assertRedirect($project->url('ar'));
    }

    public function test_records_without_an_arabic_slug_use_the_english_slug_in_arabic(): void
    {
        $project = Project::factory()->withoutArabic()->create(['slug' => ['en' => 'ruwais-solar-carports']]);

        $this->assertSame(url('/ar/projects/ruwais-solar-carports'), $project->url('ar'));
        $this->get($project->url('ar'))->assertOk();
    }

    public function test_pages_declare_their_canonical_url_and_hreflang_alternates(): void
    {
        $project = Project::factory()->create();

        $this->get($project->url('ar'))
            ->assertSee('<link rel="canonical" href="'.$project->url('ar').'">', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="'.$project->url('en').'">', false)
            ->assertSee('<link rel="alternate" hreflang="ar" href="'.$project->url('ar').'">', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.$project->url('en').'">', false)
            ->assertSee('<meta property="og:locale" content="ar_AE">', false);
    }

    public function test_projects_can_be_filtered_by_sector_using_each_languages_slug(): void
    {
        $transport = Sector::factory()->create([
            'name' => ['en' => 'Transport', 'ar' => 'النقل'],
            'slug' => ['en' => 'transport', 'ar' => 'النقل'],
        ]);
        Project::factory()->for($transport)->create(['title' => ['en' => 'Interchange upgrade', 'ar' => 'تطوير تقاطع']]);
        Project::factory()->create(['title' => ['en' => 'School campus', 'ar' => 'حرم مدرسي']]);

        $this->get('/en/projects?sector=transport')
            ->assertOk()
            ->assertSee('Interchange upgrade')
            ->assertDontSee('School campus')
            ->assertSee('hreflang="ar" href="'.url('/ar/projects?sector='.rawurlencode('النقل')).'"', false);

        $this->get('/ar/projects?sector='.rawurlencode('النقل'))
            ->assertOk()
            ->assertSee('تطوير تقاطع')
            ->assertDontSee('حرم مدرسي');
    }
}
