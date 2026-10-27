<?php

namespace App\Models;

use App\Enums\ActivityEvent;
use App\Enums\ProposalRequestStatus;
use App\Exceptions\InvalidStatusTransition;
use App\Models\Concerns\LogsActivity;
use App\Support\Activity\ActivityLogger;
use Database\Factories\ProposalRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * A request for proposal (RFP) submitted through the website.
 *
 * Status changes go through transitionTo() so that the workflow rules are
 * enforced in one place, whichever screen triggers them.
 */
#[Fillable([
    'name', 'company', 'email', 'phone', 'sector_id', 'project_location', 'message', 'locale',
    'attachment_disk', 'attachment_path', 'attachment_original_name', 'attachment_mime', 'attachment_size',
    'assigned_to', 'internal_notes',
])]
class ProposalRequest extends Model
{
    /** @use HasFactory<ProposalRequestFactory> */
    use HasFactory, LogsActivity;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ProposalRequestStatus::New->value,
    ];

    /**
     * Status changes get their own log entries.
     *
     * @var list<string>
     */
    protected array $activityIgnored = ['status', 'status_changed_at'];

    /**
     * Personal data and free text: the log records that they changed, not their content.
     *
     * @var list<string>
     */
    protected array $activitySensitive = [
        'name', 'email', 'phone', 'message', 'internal_notes',
        'attachment_path', 'attachment_original_name',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProposalRequestStatus::class,
            'status_changed_at' => 'datetime',
            'attachment_size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (self $proposalRequest): void {
            $proposalRequest->forceFill([
                'reference' => sprintf('RFP-%s-%05d', $proposalRequest->created_at->format('Y'), $proposalRequest->getKey()),
            ])->saveQuietly();
        });

        static::updated(function (self $proposalRequest): void {
            if ($proposalRequest->wasChanged('status')) {
                ActivityLogger::log(ActivityEvent::StatusChanged, $proposalRequest, [
                    'from' => $proposalRequest->getRawOriginal('status'),
                    'to' => $proposalRequest->status->value,
                ]);
            }
        });

        static::deleted(function (self $proposalRequest): void {
            $proposalRequest->deleteAttachmentFile();
        });
    }

    /**
     * @return BelongsTo<Sector, $this>
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function transitionTo(ProposalRequestStatus $status): void
    {
        if (! $this->status->canTransitionTo($status)) {
            throw InvalidStatusTransition::between($this->status, $status);
        }

        $this->forceFill([
            'status' => $status,
            'status_changed_at' => now(),
        ])->save();
    }

    #[Scope]
    protected function open(Builder $query): void
    {
        $query->where('status', '!=', ProposalRequestStatus::Closed->value);
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment_path);
    }

    /**
     * File name offered when staff download the attachment. The stored file
     * has a random name; the original name is only used for display.
     */
    public function attachmentDownloadName(): string
    {
        $extension = pathinfo((string) $this->attachment_path, PATHINFO_EXTENSION);
        $original = pathinfo((string) $this->attachment_original_name, PATHINFO_FILENAME);
        $base = Str::slug($original) ?: 'attachment';

        return "{$this->reference}-{$base}".($extension !== '' ? ".{$extension}" : '');
    }

    public function deleteAttachmentFile(): void
    {
        if ($this->hasAttachment()) {
            Storage::disk($this->attachment_disk)->delete($this->attachment_path);
        }
    }

    public function activityLabel(): string
    {
        return $this->reference ?? 'RFP #'.$this->getKey();
    }
}
