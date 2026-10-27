<?php

namespace Tests\Feature\Localization;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Support\Localization\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LocalizedAttributesTest extends TestCase
{
    use RefreshDatabase;

    public function test_translations_are_stored_as_a_json_object_keyed_by_locale(): void
    {
        $project = Project::factory()->create([
            'title' => ['en' => 'Corniche Residences Tower', 'ar' => 'برج كورنيش ريزيدنسز'],
        ]);

        $stored = DB::table('projects')->where('id', $project->id)->value('title');

        $this->assertSame(['en' => 'Corniche Residences Tower', 'ar' => 'برج كورنيش ريزيدنسز'], json_decode($stored, true));
        $this->assertSame(['en' => 'Corniche Residences Tower', 'ar' => 'برج كورنيش ريزيدنسز'], $project->fresh()->title->toArray());
    }

    public function test_reading_in_arabic_falls_back_to_english_when_the_translation_is_missing(): void
    {
        $project = Project::factory()->withoutArabic()->create(['title' => ['en' => 'Ruwais Solar Carports']]);

        App::setLocale('ar');

        $this->assertSame('Ruwais Solar Carports', (string) $project->title);
        $this->assertSame('Ruwais Solar Carports', $project->translate('title'));
        $this->assertTrue($project->title->isFallback());
    }

    public function test_completeness_lists_the_required_attributes_missing_in_arabic(): void
    {
        $project = Project::factory()->create();
        $project->update([
            'summary' => ['en' => $project->summary->in('en')],
            'body' => ['en' => $project->body->in('en')],
        ]);

        $completeness = $project->translationCompleteness(Locale::Arabic);

        $this->assertFalse($completeness->isComplete());
        $this->assertSame(['summary', 'body'], $completeness->missing);
        $this->assertSame('3/5', $completeness->summary());
        $this->assertTrue($project->translationCompleteness(Locale::English)->isComplete());
    }

    public function test_optional_seo_fields_do_not_affect_completeness(): void
    {
        $project = Project::factory()->create(['meta_title' => ['en' => 'Only English']]);

        $this->assertTrue($project->translationCompleteness(Locale::Arabic)->isComplete());
    }

    public function test_the_missing_translation_scope_finds_incomplete_records(): void
    {
        $complete = Project::factory()->create();
        $incomplete = Project::factory()->withoutArabic()->create();

        $this->assertSame([$incomplete->id], Project::query()->missingTranslation(Locale::Arabic)->pluck('id')->all());
        $this->assertSame([$complete->id], Project::query()->fullyTranslated(Locale::Arabic)->pluck('id')->all());
    }

    public function test_saving_the_same_translations_in_a_different_order_is_not_a_change(): void
    {
        $project = Project::factory()->create(['title' => ['en' => 'Bridge', 'ar' => 'جسر']]);
        $logged = ActivityLog::query()->count();

        $project->title = ['ar' => 'جسر', 'en' => 'Bridge'];

        $this->assertFalse($project->isDirty('title'));
        $project->save();
        $this->assertSame($logged, ActivityLog::query()->count());
    }

    public function test_the_arabic_site_shows_untranslated_text_in_english_marked_as_english(): void
    {
        $project = Project::factory()->create([
            'title' => ['en' => 'Ruwais Solar Carport Programme', 'ar' => 'برنامج مظلات المواقف الشمسية'],
            'body' => ['en' => '<p>The programme converts open car parks into shaded parking.</p>'],
        ]);

        $response = $this->get($project->url('ar'))
            ->assertOk()
            ->assertSee('برنامج مظلات المواقف الشمسية')
            ->assertSee('The programme converts open car parks into shaded parking.')
            ->assertSee('هذا المحتوى غير متوفر باللغة العربية حتى الآن، لذا يُعرض باللغة الإنجليزية.');

        $this->assertMatchesRegularExpression('/class="rich-text[^"]*"\s+lang="en" dir="ltr"/', $response->getContent());
    }
}
