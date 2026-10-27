<?php

namespace Tests\Feature\Authorization;

use App\Enums\Role;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Page;
use App\Models\Project;
use App\Models\ProposalRequest;
use App\Models\Setting;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The permission matrix documented in the README, checked through the
 * policies the admin panel and controllers rely on.
 */
class PolicyMatrixTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{Role, string, Closure(): (object|string), bool}>
     */
    public static function matrix(): array
    {
        $rfp = fn (): ProposalRequest => ProposalRequest::factory()->withAttachment()->create();
        $page = fn (): Page => Page::factory()->create();

        return [
            'editor cannot download an RFP attachment' => [Role::Editor, 'downloadAttachment', $rfp, false],
            'intake can download an RFP attachment' => [Role::Intake, 'downloadAttachment', $rfp, true],
            'admin can download an RFP attachment' => [Role::Admin, 'downloadAttachment', $rfp, true],

            'intake cannot edit pages' => [Role::Intake, 'update', $page, false],
            'editor can edit pages' => [Role::Editor, 'update', $page, true],
            'admin can edit pages' => [Role::Admin, 'update', $page, true],

            'intake cannot see projects' => [Role::Intake, 'viewAny', fn (): string => Project::class, false],
            'intake cannot publish news' => [Role::Intake, 'create', fn (): string => Article::class, false],
            'editor can publish news' => [Role::Editor, 'create', fn (): string => Article::class, true],

            'editor cannot see RFPs' => [Role::Editor, 'viewAny', fn (): string => ProposalRequest::class, false],
            'editor cannot update an RFP' => [Role::Editor, 'update', $rfp, false],
            'intake can update an RFP' => [Role::Intake, 'update', $rfp, true],
            'intake can export RFPs' => [Role::Intake, 'export', fn (): string => ProposalRequest::class, true],
            'editor cannot export RFPs' => [Role::Editor, 'export', fn (): string => ProposalRequest::class, false],
            'intake cannot delete an RFP' => [Role::Intake, 'delete', $rfp, false],
            'admin can delete an RFP' => [Role::Admin, 'delete', $rfp, true],
            'nobody creates RFPs in the admin' => [Role::Admin, 'create', fn (): string => ProposalRequest::class, false],

            'editor cannot manage users' => [Role::Editor, 'viewAny', fn (): string => User::class, false],
            'intake cannot manage users' => [Role::Intake, 'viewAny', fn (): string => User::class, false],
            'admin can manage users' => [Role::Admin, 'viewAny', fn (): string => User::class, true],

            'editor cannot read the activity log' => [Role::Editor, 'viewAny', fn (): string => ActivityLog::class, false],
            'admin can read the activity log' => [Role::Admin, 'viewAny', fn (): string => ActivityLog::class, true],

            'editor can change site settings' => [Role::Editor, 'manage', fn (): string => Setting::class, true],
            'intake cannot change site settings' => [Role::Intake, 'manage', fn (): string => Setting::class, false],
        ];
    }

    /**
     * @param  Closure(): (object|string)  $subject
     */
    #[DataProvider('matrix')]
    public function test_role_permissions(Role $role, string $ability, Closure $subject, bool $expected): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->assertSame($expected, $user->can($ability, $subject()));
    }

    public function test_activity_log_entries_cannot_be_changed_by_anyone(): void
    {
        $admin = $this->admin();
        $entry = ActivityLog::query()->firstOrFail();

        $this->assertFalse($admin->can('update', $entry));
        $this->assertFalse($admin->can('delete', $entry));
        $this->assertFalse($admin->can('create', ActivityLog::class));
    }

    public function test_system_pages_cannot_be_deleted_but_other_pages_can(): void
    {
        $admin = $this->admin();

        $this->assertFalse($admin->can('delete', Page::factory()->about()->create()));
        $this->assertTrue($admin->can('delete', Page::factory()->create()));
    }

    public function test_administrators_cannot_delete_their_own_account(): void
    {
        $admin = $this->admin();

        $this->assertFalse($admin->can('delete', $admin));
        $this->assertTrue($admin->can('delete', $this->editor()));
    }

    public function test_deactivated_users_lose_every_permission(): void
    {
        $admin = User::factory()->admin()->inactive()->create();

        $this->assertFalse($admin->can('viewAny', User::class));
        $this->assertFalse($admin->can('update', Page::factory()->create()));
    }
}
