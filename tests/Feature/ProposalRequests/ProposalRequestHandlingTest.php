<?php

namespace Tests\Feature\ProposalRequests;

use App\Enums\ActivityEvent;
use App\Enums\ProposalRequestStatus;
use App\Exceptions\InvalidStatusTransition;
use App\Exports\ProposalRequestCsvExport;
use App\Filament\Resources\ProposalRequests\Pages\ListProposalRequests;
use App\Filament\Resources\ProposalRequests\Pages\ViewProposalRequest;
use App\Models\ActivityLog;
use App\Models\ProposalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class ProposalRequestHandlingTest extends TestCase
{
    use RefreshDatabase;

    private ProposalRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');
        Storage::disk('private')->put('proposal-requests/2026/10/brief.pdf', '%PDF-1.4 brief');

        $this->request = ProposalRequest::factory()->withAttachment('proposal-requests/2026/10/brief.pdf')->create();
    }

    private function attachmentUrl(): string
    {
        return route('admin.proposal-requests.attachment', $this->request);
    }

    public function test_intake_staff_can_download_an_attachment_and_the_download_is_logged(): void
    {
        $intake = $this->intake();

        $response = $this->actingAs($intake)->get($this->attachmentUrl())
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertDownload("{$this->request->reference}-project-brief.pdf");

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertTrue(ActivityLog::query()
            ->where('event', ActivityEvent::AttachmentDownloaded)
            ->where('user_id', $intake->id)
            ->where('subject_id', $this->request->id)
            ->exists());
    }

    public function test_administrators_can_download_attachments(): void
    {
        $this->actingAs($this->admin())->get($this->attachmentUrl())->assertOk();
    }

    public function test_editors_cannot_download_attachments(): void
    {
        $this->actingAs($this->editor())->get($this->attachmentUrl())->assertForbidden();

        $this->assertFalse(ActivityLog::query()->where('event', ActivityEvent::AttachmentDownloaded)->exists());
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get($this->attachmentUrl())->assertRedirect('/admin/login');
    }

    public function test_the_signed_email_link_opens_the_record_for_staff(): void
    {
        $url = URL::temporarySignedRoute('admin.proposal-requests.open', now()->addDay(), ['proposalRequest' => $this->request->id]);

        $this->actingAs($this->intake())
            ->get($url)
            ->assertRedirect("/admin/proposal-requests/{$this->request->id}");
    }

    public function test_the_signed_email_link_asks_guests_to_sign_in_first(): void
    {
        $url = URL::temporarySignedRoute('admin.proposal-requests.open', now()->addDay(), ['proposalRequest' => $this->request->id]);

        $this->get($url)->assertRedirect('/admin/login');
    }

    public function test_tampered_or_expired_links_are_rejected(): void
    {
        $intake = $this->intake();
        $url = URL::temporarySignedRoute('admin.proposal-requests.open', now()->addDay(), ['proposalRequest' => $this->request->id]);

        $other = ProposalRequest::factory()->create();

        $this->actingAs($intake)->get(str_replace("/{$this->request->id}/", "/{$other->id}/", $url))->assertForbidden();
        $this->actingAs($intake)->get(str_replace("/{$this->request->id}/", '/999/', $url))->assertForbidden();
        $this->get(str_replace("/{$this->request->id}/", "/{$other->id}/", $url))->assertForbidden();
        $this->actingAs($intake)->get(route('admin.proposal-requests.open', $this->request))->assertForbidden();

        $this->travel(2)->days();
        $this->actingAs($intake)->get($url)->assertForbidden();
    }

    public function test_editors_cannot_open_a_signed_link(): void
    {
        $url = URL::temporarySignedRoute('admin.proposal-requests.open', now()->addDay(), ['proposalRequest' => $this->request->id]);

        $this->actingAs($this->editor())->get($url)->assertForbidden();
    }

    public function test_the_status_follows_the_workflow(): void
    {
        $this->request->transitionTo(ProposalRequestStatus::InReview);
        $this->request->transitionTo(ProposalRequestStatus::ProposalSent);
        $this->request->transitionTo(ProposalRequestStatus::Closed);

        $this->assertSame(ProposalRequestStatus::Closed, $this->request->fresh()->status);
        $this->assertNotNull($this->request->status_changed_at);

        $this->expectException(InvalidStatusTransition::class);
        $this->request->transitionTo(ProposalRequestStatus::ProposalSent);
    }

    public function test_a_new_request_cannot_skip_the_review(): void
    {
        $this->expectException(InvalidStatusTransition::class);

        $this->request->transitionTo(ProposalRequestStatus::ProposalSent);
    }

    public function test_intake_staff_move_requests_through_the_workflow_from_the_admin(): void
    {
        $intake = $this->intake();
        $this->actingAs($intake);

        Livewire::test(ViewProposalRequest::class, ['record' => $this->request->getRouteKey()])
            ->assertActionVisible('status_in_review')
            ->assertActionHidden('status_proposal_sent')
            ->callAction('status_in_review')
            ->assertHasNoActionErrors();

        $this->assertSame(ProposalRequestStatus::InReview, $this->request->fresh()->status);

        $entry = ActivityLog::query()->where('event', ActivityEvent::StatusChanged)->sole();
        $this->assertSame($intake->id, $entry->user_id);
        $this->assertSame(['from' => 'new', 'to' => 'in_review'], $entry->properties);
    }

    public function test_intake_staff_can_assign_a_request_to_themselves(): void
    {
        $intake = $this->intake();
        $this->actingAs($intake);

        Livewire::test(ListProposalRequests::class)
            ->callTableAction('assign_to_me', $this->request);

        $this->assertTrue($this->request->fresh()->assignee->is($intake));
    }

    public function test_the_csv_export_handles_arabic_text_and_neutralises_formulas(): void
    {
        $this->request->update([
            'name' => 'عائشة الكعبي',
            'company' => '=HYPERLINK("https://spam.masar.test")',
        ]);
        $this->actingAs($this->intake());

        $response = app(ProposalRequestCsvExport::class)->download(ProposalRequest::query());

        ob_start();
        $response->sendContent();
        $csv = (string) ob_get_clean();

        $this->assertStringStartsWith("\u{FEFF}Reference,Received,Status", $csv);
        $this->assertStringContainsString('عائشة الكعبي', $csv);
        $this->assertStringContainsString($this->request->reference, $csv);
        $this->assertStringContainsString('"\'=HYPERLINK(""https://spam.masar.test"")"', $csv);
        $this->assertSame(1, ActivityLog::query()->where('event', ActivityEvent::Exported)->count());
    }

    public function test_only_staff_allowed_to_export_see_the_export_action(): void
    {
        $this->actingAs($this->intake());
        Livewire::test(ListProposalRequests::class)->assertActionVisible('export');

        $this->actingAs($this->admin());
        Livewire::test(ListProposalRequests::class)
            ->callAction('export')
            ->assertFileDownloaded();
    }

    public function test_deleting_a_request_also_deletes_its_attachment(): void
    {
        $this->request->delete();

        Storage::disk('private')->assertMissing('proposal-requests/2026/10/brief.pdf');
    }
}
