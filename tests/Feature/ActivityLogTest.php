<?php

namespace Tests\Feature;

use App\Enums\ActivityEvent;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Support\Activity\ActivityChanges;
use App\Support\Activity\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use LogicException;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_changes_are_logged_with_the_user_and_the_changed_fields(): void
    {
        $editor = $this->editor();
        $this->actingAs($editor);

        $project = Project::factory()->create(['title' => ['en' => 'Old title', 'ar' => 'عنوان قديم']]);
        $project->update(['title' => ['en' => 'New title', 'ar' => 'عنوان قديم'], 'is_featured' => true]);
        $project->delete();

        $entries = ActivityLog::query()->where('subject_type', 'project')->orderBy('id')->get();

        $this->assertSame([ActivityEvent::Created, ActivityEvent::Updated, ActivityEvent::Deleted], $entries->pluck('event')->all());
        $this->assertSame([$editor->id], $entries->pluck('user_id')->unique()->values()->all());
        $this->assertSame('New title', $entries[2]->subject_label);

        $update = $entries[1]->properties;
        $this->assertSame(['en' => 'Old title', 'ar' => 'عنوان قديم'], $update['old']['title']);
        $this->assertSame(['en' => 'New title', 'ar' => 'عنوان قديم'], $update['new']['title']);
        $this->assertSame(['title', 'is_featured'], array_keys($update['new']));
        $this->assertArrayNotHasKey('updated_at', $update['new']);
    }

    public function test_sensitive_fields_are_never_written_to_the_log(): void
    {
        $user = User::factory()->create(['password' => 'first-Secret-1']);
        $user->update(['password' => 'second-Secret-2', 'name' => 'Renamed']);

        $update = ActivityLog::query()->where('subject_type', 'user')->where('event', ActivityEvent::Updated)->sole();

        $this->assertSame(['name'], array_keys($update->properties['new']));
        $this->assertSame(['password'], $update->properties['redacted']);
        $this->assertStringNotContainsString($user->password, json_encode(ActivityLog::all()->pluck('properties')));

        $request = ProposalRequest::factory()->create(['email' => 'client@clients.masar.test', 'message' => 'Confidential scope']);
        $created = ActivityLog::query()->where('subject_type', 'proposal_request')->sole()->properties;

        $this->assertArrayNotHasKey('email', $created['attributes']);
        $this->assertArrayNotHasKey('message', $created['attributes']);
        $this->assertContains('email', $created['redacted']);
        $this->assertSame($request->reference, $created['attributes']['reference']);
    }

    public function test_saving_without_changes_writes_nothing(): void
    {
        $project = Project::factory()->create();
        $count = ActivityLog::query()->count();

        $project->save();
        $project->update(['title' => $project->title->toArray()]);

        $this->assertSame($count, ActivityLog::query()->count());
    }

    public function test_logging_can_be_paused_for_bulk_imports(): void
    {
        ActivityLogger::withoutLogging(fn () => Project::factory()->count(2)->create());

        $this->assertSame(0, ActivityLog::query()->where('subject_type', 'project')->count());
    }

    public function test_entries_cannot_be_modified(): void
    {
        Project::factory()->create();

        $this->expectException(LogicException::class);

        ActivityLog::query()->firstOrFail()->update(['event' => ActivityEvent::Deleted]);
    }

    public function test_changes_are_presented_field_by_field(): void
    {
        $project = Project::factory()->create(['title' => ['en' => 'Before', 'ar' => 'قبل']]);
        $project->update(['title' => ['en' => 'After', 'ar' => 'بعد']]);

        $rows = ActivityChanges::rows(ActivityLog::query()->where('event', ActivityEvent::Updated)->sole());

        $this->assertSame([['attribute' => 'Title', 'old' => "EN: Before\nAR: قبل", 'new' => "EN: After\nAR: بعد"]], $rows);
    }

    public function test_administrators_can_browse_the_log(): void
    {
        $this->actingAs($this->admin());
        $project = Project::factory()->create();

        $entry = ActivityLog::query()->where('subject_type', 'project')->sole();

        Livewire::test(ListActivityLogs::class)
            ->assertCanSeeTableRecords([$entry])
            ->filterTable('event', [ActivityEvent::Deleted->value])
            ->assertCanNotSeeTableRecords([$entry]);

        $this->assertSame($project->translate('title', 'en'), $entry->subject_label);
    }
}
