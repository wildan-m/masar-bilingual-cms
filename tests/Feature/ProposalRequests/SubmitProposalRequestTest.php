<?php

namespace Tests\Feature\ProposalRequests;

use App\Models\Page;
use App\Models\ProposalRequest;
use App\Models\Sector;
use App\Notifications\NewProposalRequestReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubmitProposalRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');
        Storage::fake('public');
        Notification::fake();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validInput(array $overrides = []): array
    {
        return [
            'name' => 'Priya Raman',
            'company' => 'Greenfield International School',
            'email' => 'priya.raman@clients.masar.test',
            'phone' => '+971 55 000 4182',
            'sector_id' => Sector::factory()->create()->id,
            'project_location' => 'Mohammed Bin Zayed City',
            'message' => 'Design of a two-storey extension with eight classrooms and a multipurpose hall.',
            ...$overrides,
        ];
    }

    public function test_a_valid_request_is_stored_with_its_attachment_on_the_private_disk(): void
    {
        $intake = $this->intake();
        $editor = $this->editor();
        $brief = UploadedFile::fake()->create('Extension brief.pdf', 800, 'application/pdf');

        $this->post('/en/request-for-proposal', $this->validInput(['attachment' => $brief]))
            ->assertRedirect('/en/request-for-proposal/thank-you')
            ->assertSessionHas('rfp_reference');

        $request = ProposalRequest::query()->sole();

        $this->assertMatchesRegularExpression('/^RFP-\d{4}-\d{5}$/', $request->reference);
        $this->assertSame('en', $request->locale);
        $this->assertSame('private', $request->attachment_disk);
        $this->assertSame('Extension brief.pdf', $request->attachment_original_name);
        $this->assertMatchesRegularExpression('#^proposal-requests/\d{4}/\d{2}/[A-Za-z0-9]{40}\.pdf$#', $request->attachment_path);
        $this->assertStringNotContainsString('Extension', $request->attachment_path);

        Storage::disk('private')->assertExists($request->attachment_path);
        $this->assertSame([], Storage::disk('public')->allFiles());

        Notification::assertSentTo($intake, NewProposalRequestReceived::class);
        Notification::assertNotSentTo($editor, NewProposalRequestReceived::class);
    }

    public function test_the_attachment_is_not_reachable_through_a_public_url(): void
    {
        $this->post('/en/request-for-proposal', $this->validInput([
            'attachment' => UploadedFile::fake()->create('brief.pdf', 100, 'application/pdf'),
        ]));

        $path = ProposalRequest::query()->sole()->attachment_path;

        $this->assertFalse(Str::startsWith(Storage::disk('private')->path($path), public_path()));
        $this->get('/storage/'.$path)->assertNotFound();
    }

    public function test_the_notification_links_to_the_admin_record_through_a_signed_url(): void
    {
        $intake = $this->intake();

        $this->post('/en/request-for-proposal', $this->validInput());

        $request = ProposalRequest::query()->sole();

        Notification::assertSentTo($intake, NewProposalRequestReceived::class, function (NewProposalRequestReceived $notification) use ($request, $intake): bool {
            $url = $notification->signedUrl();
            $mail = $notification->toMail($intake);

            return $notification->proposalRequest->is($request)
                && URL::hasValidSignature(Request::create($url))
                && str_contains($url, "/admin/proposal-requests/{$request->id}/open")
                && $mail->actionUrl === $url
                && $notification->locale === 'en';
        });
    }

    public function test_administrators_are_notified_when_there_is_no_intake_staff(): void
    {
        $admin = $this->admin();

        $this->post('/en/request-for-proposal', $this->validInput());

        Notification::assertSentTo($admin, NewProposalRequestReceived::class);
    }

    public function test_validation_messages_are_in_arabic_on_the_arabic_site(): void
    {
        $this->from('/ar/'.rawurlencode('تواصل-معنا'))
            ->post('/ar/request-for-proposal', ['email' => 'not-an-email'])
            ->assertRedirect('/ar/'.rawurlencode('تواصل-معنا').'#request-for-proposal')
            ->assertSessionHasErrors([
                'name' => 'حقل الاسم الكامل مطلوب.',
                'email' => 'يجب أن يكون البريد الإلكتروني عنوان بريد إلكتروني صحيحًا.',
                'message' => 'حقل وصف المشروع مطلوب.',
            ]);

        $this->assertSame(0, ProposalRequest::query()->count());
    }

    public function test_validation_messages_are_in_english_on_the_english_site(): void
    {
        $this->post('/en/request-for-proposal', [])
            ->assertSessionHasErrors([
                'name' => 'The full name field is required.',
                'phone' => 'The phone number field is required.',
            ]);
    }

    public function test_only_pdf_word_and_excel_attachments_are_accepted(): void
    {
        foreach (['drawing.png' => 'image/png', 'setup.exe' => 'application/x-msdownload', 'brief.pdf.php' => 'text/x-php'] as $name => $mime) {
            $this->post('/en/request-for-proposal', $this->validInput([
                'attachment' => UploadedFile::fake()->create($name, 10, $mime),
            ]))->assertSessionHasErrors('attachment');
        }

        foreach (['scope.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'boq.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'] as $name => $mime) {
            $this->post('/en/request-for-proposal', $this->validInput([
                'attachment' => UploadedFile::fake()->create($name, 10, $mime),
            ]))->assertSessionHasNoErrors();
        }

        $this->assertSame(2, ProposalRequest::query()->count());
        $this->assertCount(2, Storage::disk('private')->allFiles());
    }

    public function test_attachments_larger_than_ten_megabytes_are_rejected(): void
    {
        $this->post('/en/request-for-proposal', $this->validInput([
            'attachment' => UploadedFile::fake()->create('drawings.pdf', 10 * 1024 + 1, 'application/pdf'),
        ]))->assertSessionHasErrors('attachment');

        $this->assertSame([], Storage::disk('private')->allFiles());
    }

    public function test_phone_numbers_typed_with_arabic_indic_digits_are_accepted(): void
    {
        $this->post('/ar/request-for-proposal', $this->validInput(['phone' => '+٩٧١ ٥٠ ٠٠٠ ٢٢٣١']))
            ->assertSessionHasNoErrors();

        $this->assertSame('+971 50 000 2231', ProposalRequest::query()->sole()->phone);
        $this->assertSame('ar', ProposalRequest::query()->sole()->locale);
    }

    public function test_submissions_that_fill_the_honeypot_are_discarded_silently(): void
    {
        $this->intake();

        $this->post('/en/request-for-proposal', $this->validInput(['website' => 'https://spam.masar.test']))
            ->assertRedirect('/en/request-for-proposal/thank-you')
            ->assertSessionMissing('rfp_reference');

        $this->assertSame(0, ProposalRequest::query()->count());
        Notification::assertNothingSent();
    }

    public function test_submissions_are_limited_to_five_per_minute_per_address(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post('/ar/request-for-proposal', $this->validInput())->assertRedirect();
        }

        $this->post('/ar/request-for-proposal', $this->validInput())
            ->assertStatus(429)
            ->assertSee('أرسلت عدة طلبات خلال وقت قصير');

        $this->assertSame(5, ProposalRequest::query()->count());

        $this->travel(61)->seconds();

        $this->post('/ar/request-for-proposal', $this->validInput())->assertRedirect();
        $this->assertSame(6, ProposalRequest::query()->count());
    }

    public function test_the_thank_you_page_is_shown_in_the_visitors_language(): void
    {
        $this->followingRedirects()
            ->post('/ar/request-for-proposal', $this->validInput())
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('تم استلام طلبك')
            ->assertSee(ProposalRequest::query()->sole()->reference);
    }

    public function test_the_contact_page_shows_the_form_with_localized_labels(): void
    {
        $contact = Page::factory()->contact()->create();

        $this->get($contact->url('ar'))
            ->assertOk()
            ->assertSee('طلب عرض فني ومالي')
            ->assertSee('name="website"', false)
            ->assertSee('enctype="multipart/form-data"', false);
    }
}
