<?php

namespace Tests\Feature\Admin;

use App\Enums\ClientType;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Discipline;
use App\Models\Project;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class BilingualEditingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->editor());
    }

    /**
     * @return array<string, mixed>
     */
    private function projectForm(array $overrides = []): array
    {
        return [
            'title' => ['en' => 'Saadiyat Waterfront Pavilion', 'ar' => 'جناح الواجهة البحرية في السعديات'],
            'slug' => ['en' => 'saadiyat-waterfront-pavilion', 'ar' => 'جناح-الواجهة-البحرية-في-السعديات'],
            'location' => ['en' => 'Saadiyat Island, Abu Dhabi', 'ar' => 'جزيرة السعديات، أبوظبي'],
            'summary' => ['en' => 'A shaded events pavilion on the beach.', 'ar' => 'جناح مظلل للفعاليات على الشاطئ.'],
            'body' => ['en' => '<p>Steel canopy and timber deck.</p>', 'ar' => '<p>مظلة فولاذية وأرضية خشبية.</p>'],
            'sector_id' => Sector::factory()->create()->id,
            'client_type' => ClientType::Developer->value,
            'completion_year' => 2025,
            'disciplines' => [Discipline::factory()->create()->id],
            'is_published' => true,
            ...$overrides,
        ];
    }

    public function test_editors_create_a_project_in_both_languages_from_one_form(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm($this->projectForm())
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::query()->sole();
        $stored = json_decode(DB::table('projects')->where('id', $project->id)->value('title'), true);

        $this->assertSame(['en' => 'Saadiyat Waterfront Pavilion', 'ar' => 'جناح الواجهة البحرية في السعديات'], $stored);
        $this->assertSame('جناح-الواجهة-البحرية-في-السعديات', $project->slugFor('ar'));
        $this->assertCount(1, $project->disciplines);
    }

    public function test_english_is_required_while_arabic_can_follow_later(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm($this->projectForm([
                'title' => ['en' => '', 'ar' => 'عنوان'],
                'slug' => ['en' => '', 'ar' => ''],
            ]))
            ->call('create')
            ->assertHasFormErrors(['title.en' => 'required', 'slug.en' => 'required']);

        Livewire::test(CreateProject::class)
            ->fillForm($this->projectForm([
                'title' => ['en' => 'English only', 'ar' => null],
                'slug' => ['en' => 'english-only', 'ar' => null],
                'summary' => ['en' => 'Summary', 'ar' => null],
            ]))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(['title', 'slug', 'summary'], Project::query()->sole()->translationCompleteness('ar')->missing);
    }

    public function test_slugs_must_be_unique_within_each_language(): void
    {
        Project::factory()->create(['slug' => ['en' => 'taken', 'ar' => 'محجوز']]);

        Livewire::test(CreateProject::class)
            ->fillForm($this->projectForm(['slug' => ['en' => 'taken', 'ar' => 'محجوز']]))
            ->call('create')
            ->assertHasFormErrors(['slug.en', 'slug.ar']);

        $this->assertSame(1, Project::query()->count());
    }

    public function test_slugs_must_not_contain_spaces_or_slashes(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm($this->projectForm(['slug' => ['en' => 'has space', 'ar' => 'مع/شرطة']]))
            ->call('create')
            ->assertHasFormErrors(['slug.en' => 'regex', 'slug.ar' => 'regex']);
    }

    public function test_typing_a_title_suggests_a_slug_in_the_same_language(): void
    {
        Livewire::test(CreateProject::class)
            ->set('data.title.en', 'Stormwater Pumping Station')
            ->set('data.title.ar', 'محطة ضخ مياه الأمطار')
            ->assertSchemaStateSet([
                'slug.en' => 'stormwater-pumping-station',
                'slug.ar' => 'محطة-ضخ-مياه-الأمطار',
            ]);
    }

    public function test_arabic_fields_are_typed_right_to_left(): void
    {
        $project = Project::factory()->create();

        $html = $this->get("/admin/projects/{$project->id}/edit")->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<input[^>]*dir="rtl"[^>]*lang="ar"/s', $html);
        $this->assertMatchesRegularExpression('/<textarea[^>]*dir="rtl"[^>]*lang="ar"/s', $html);
        $this->assertMatchesRegularExpression('/class="[^"]*fi-fo-rich-editor-main[^"]*"[^>]*dir="rtl"|dir="rtl"[^>]*class="[^"]*fi-fo-rich-editor-main/s', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*dir="ltr"[^>]*lang="en"/s', $html);
    }

    public function test_untranslated_fields_are_flagged_in_the_edit_form(): void
    {
        $project = Project::factory()->withoutArabic()->create();

        $this->get("/admin/projects/{$project->id}/edit")
            ->assertOk()
            ->assertSee('Arabic translation incomplete')
            ->assertSee('Not translated: visitors see the English text');

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertSchemaStateSet(['title.en' => $project->title->in('en'), 'title.ar' => null]);
    }

    public function test_the_project_list_shows_translation_completeness_per_record(): void
    {
        $complete = Project::factory()->create();
        $partial = Project::factory()->create();
        $partial->update(['summary' => ['en' => 'English summary'], 'body' => ['en' => '<p>English body</p>']]);
        $untranslated = Project::factory()->withoutArabic()->create();

        Livewire::test(ListProjects::class)
            ->assertTableColumnStateSet('translation_ar', 'Complete', $complete)
            ->assertTableColumnStateSet('translation_ar', '3 of 5', $partial)
            ->assertTableColumnStateSet('translation_ar', 'Missing', $untranslated)
            ->filterTable('ar_translation', false)
            ->assertCanSeeTableRecords([$partial, $untranslated])
            ->assertCanNotSeeTableRecords([$complete]);
    }
}
