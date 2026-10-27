<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ActivityEvent;
use App\Models\ProposalRequest;
use App\Support\Activity\ActivityLogger;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams an RFP attachment from the private disk to authorised staff.
 * Attachments never have a public URL; every download is logged.
 */
final class ProposalRequestAttachmentController
{
    public function __invoke(ProposalRequest $proposalRequest): StreamedResponse
    {
        Gate::authorize('downloadAttachment', $proposalRequest);

        $disk = Storage::disk($proposalRequest->attachment_disk);

        abort_unless($disk->exists($proposalRequest->attachment_path), 404);

        ActivityLogger::log(ActivityEvent::AttachmentDownloaded, $proposalRequest);

        return $disk->download($proposalRequest->attachment_path, $proposalRequest->attachmentDownloadName(), [
            'Content-Type' => $proposalRequest->attachment_mime ?: 'application/octet-stream',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
