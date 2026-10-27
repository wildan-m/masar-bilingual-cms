<?php

namespace App\Actions;

use App\Enums\Role;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Notifications\NewProposalRequestReceived;
use App\Support\Localization\Locale;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Stores a request for proposal submitted on the website and lets the
 * intake team know about it.
 */
final class SubmitProposalRequest
{
    /**
     * @param  array<string, mixed>  $data  Validated form input.
     */
    public function handle(array $data, ?UploadedFile $attachment, Locale $locale): ProposalRequest
    {
        $stored = $attachment instanceof UploadedFile ? $this->storeAttachment($attachment) : [];

        try {
            $proposalRequest = DB::transaction(fn (): ProposalRequest => ProposalRequest::create([
                ...Arr::only($data, ['name', 'company', 'email', 'phone', 'sector_id', 'project_location', 'message']),
                ...$stored,
                'locale' => $locale->value,
            ]));
        } catch (Throwable $exception) {
            // Do not leave an orphaned upload behind when the record was not saved.
            if ($stored !== []) {
                Storage::disk($stored['attachment_disk'])->delete($stored['attachment_path']);
            }

            throw $exception;
        }

        $this->notifyIntakeTeam($proposalRequest);

        return $proposalRequest;
    }

    /**
     * Store the upload on the private disk under a random name. The original
     * file name is kept for display only.
     *
     * @return array<string, mixed>
     */
    private function storeAttachment(UploadedFile $file): array
    {
        $disk = config('masar.rfp.attachment_disk');
        $directory = config('masar.rfp.attachment_directory').'/'.now()->format('Y/m');

        $path = $file->store($directory, ['disk' => $disk]);

        if ($path === false) {
            throw new RuntimeException('The RFP attachment could not be stored.');
        }

        return [
            'attachment_disk' => $disk,
            'attachment_path' => $path,
            'attachment_original_name' => $this->cleanFileName($file->getClientOriginalName()),
            'attachment_mime' => $file->getMimeType(),
            'attachment_size' => $file->getSize(),
        ];
    }

    private function cleanFileName(string $name): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F\/\\\\]+/u', '', basename($name)) ?? '';

        return Str::limit(trim($name), 150, '') ?: 'attachment';
    }

    private function notifyIntakeTeam(ProposalRequest $proposalRequest): void
    {
        $recipients = User::query()->active()->where('role', Role::Intake)->get();

        // Never let a request go unnoticed: fall back to administrators.
        if ($recipients->isEmpty()) {
            $recipients = User::query()->active()->where('role', Role::Admin)->get();
        }

        Notification::send($recipients, (new NewProposalRequestReceived($proposalRequest))->locale('en'));
    }
}
