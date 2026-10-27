<?php

namespace Tests\Feature\Seeders;

use App\Enums\Role;
use App\Models\Project;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Support\Localization\Locale;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_creates_an_account_per_role_and_bilingual_content(): void
    {
        Storage::fake('public');
        Storage::fake('private');

        $this->seed(DatabaseSeeder::class);

        foreach (Role::cases() as $role) {
            $this->assertTrue(User::query()->where('role', $role)->where('email', 'like', '%@masar.test')->exists());
        }

        $this->assertSame(9, Project::query()->count());
        $this->assertSame(1, Project::query()->missingTranslation(Locale::Arabic)->count());
        $this->assertSame(6, ProposalRequest::query()->count());
        $this->assertCount(2, Storage::disk('private')->allFiles());
        $this->assertNotEmpty(Storage::disk('public')->files('projects'));

        // Running the seeder again keeps the accounts and does not duplicate content.
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, User::query()->count());
        $this->assertSame(9, Project::query()->count());
    }

    public function test_every_admin_screen_renders_with_the_seeded_content(): void
    {
        Storage::fake('public');
        Storage::fake('private');
        $this->seed(DatabaseSeeder::class);

        $this->actingAs(User::query()->where('email', 'admin@masar.test')->sole());

        $request = ProposalRequest::query()->whereNotNull('attachment_path')->firstOrFail();

        foreach ([
            '/admin', '/admin/pages', '/admin/pages/create', '/admin/disciplines', '/admin/projects', '/admin/projects/create',
            '/admin/projects/1/edit', '/admin/sectors', '/admin/news', '/admin/news/1/edit', '/admin/news-categories',
            '/admin/proposal-requests', "/admin/proposal-requests/{$request->id}", "/admin/proposal-requests/{$request->id}/edit",
            '/admin/users', '/admin/activity-logs', '/admin/site-settings', '/admin/profile',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
