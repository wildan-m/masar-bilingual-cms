<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\ProposalRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The admin panel enforces the same policies: screens a role may not use
 * are hidden from its navigation and refuse direct access.
 */
class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_intake_staff_cannot_edit_pages(): void
    {
        $page = Page::factory()->create();

        $this->actingAs($this->intake())->get("/admin/pages/{$page->id}/edit")->assertForbidden();
        $this->actingAs($this->intake())->get('/admin/pages')->assertForbidden();
        $this->actingAs($this->intake())->get('/admin/site-settings')->assertForbidden();
    }

    public function test_editors_cannot_open_rfps_users_or_the_activity_log(): void
    {
        $request = ProposalRequest::factory()->create();
        $editor = $this->editor();

        $this->actingAs($editor)->get('/admin/proposal-requests')->assertForbidden();
        $this->actingAs($editor)->get("/admin/proposal-requests/{$request->id}")->assertForbidden();
        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
        $this->actingAs($editor)->get('/admin/activity-logs')->assertForbidden();
    }

    public function test_administrators_can_open_every_screen(): void
    {
        $page = Page::factory()->create();
        $request = ProposalRequest::factory()->create();
        $admin = $this->admin();

        foreach (["/admin/pages/{$page->id}/edit", "/admin/proposal-requests/{$request->id}", '/admin/users', '/admin/activity-logs', '/admin/site-settings'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_editors_only_see_content_screens_in_the_navigation(): void
    {
        $this->actingAs($this->editor())->get('/admin')
            ->assertOk()
            ->assertSee('Projects')
            ->assertSee('Site settings')
            ->assertDontSee('Requests for proposal')
            ->assertDontSee('Activity log');
    }

    public function test_intake_staff_only_see_rfp_screens_in_the_navigation(): void
    {
        $this->actingAs($this->intake())->get('/admin')
            ->assertOk()
            ->assertSee('Requests for proposal')
            ->assertDontSee('Site settings')
            ->assertDontSee('News categories')
            ->assertDontSee('Activity log');
    }

    public function test_deactivated_users_cannot_use_the_panel(): void
    {
        $this->actingAs(User::factory()->admin()->inactive()->create())->get('/admin')->assertForbidden();
    }

    public function test_guests_are_asked_to_sign_in(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }
}
