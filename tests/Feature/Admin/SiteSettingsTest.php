<?php

namespace Tests\Feature\Admin;

use App\Enums\ActivityEvent;
use App\Filament\Pages\ManageSiteSettings;
use App\Models\ActivityLog;
use App\Support\Settings\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_editors_update_bilingual_settings_shown_on_the_website(): void
    {
        $this->actingAs($this->editor());

        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'company_name' => ['en' => 'Masar Engineering Consultants', 'ar' => 'مسار للاستشارات الهندسية'],
                'hero_title' => ['en' => 'Engineering that lasts', 'ar' => 'هندسة تدوم'],
                'phone' => '+971 2 000 0148',
                'linkedin_url' => 'https://www.linkedin.com/company/masar-engineering-consultants',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = app(SiteSettings::class);
        $this->assertSame('هندسة تدوم', $settings->text('hero_title')->get('ar'));
        $this->assertSame('+971 2 000 0148', $settings->get('phone'));

        $this->get('/ar')->assertSee('هندسة تدوم')->assertSee('لينكدإن');
        $this->get('/en')->assertSee('Engineering that lasts');

        $this->assertTrue(ActivityLog::query()
            ->where('event', ActivityEvent::Created)
            ->where('subject_type', 'setting')
            ->where('subject_label', 'hero_title')
            ->exists());
    }

    public function test_untranslated_settings_fall_back_to_english(): void
    {
        app(SiteSettings::class)->save(['tagline' => ['en' => 'Engineering consultancy']]);

        $this->get('/ar')
            ->assertOk()
            ->assertSee('<span lang="en" dir="ltr">Engineering consultancy</span>', false);
    }

    public function test_the_english_company_name_is_required(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(ManageSiteSettings::class)
            ->fillForm(['company_name' => ['en' => '', 'ar' => 'مسار'], 'hero_title' => ['en' => 'Headline']])
            ->call('save')
            ->assertHasFormErrors(['company_name.en' => 'required']);
    }
}
