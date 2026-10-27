<?php

namespace App\Models;

use App\Enums\ActivityEvent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

/**
 * An append-only audit entry. Entries are never edited; old entries are
 * pruned after the retention period (see config/masar.php).
 */
#[Fillable(['user_id', 'event', 'subject_type', 'subject_id', 'subject_label', 'properties', 'ip_address'])]
class ActivityLog extends Model
{
    use MassPrunable;

    public const UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event' => ActivityEvent::class,
            'properties' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Activity log entries cannot be modified.');
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Names of the attributes the entry is about.
     *
     * @return list<string>
     */
    public function attributeNames(): array
    {
        $properties = $this->properties ?? [];

        return array_values(array_unique([
            ...array_keys($properties['new'] ?? $properties['attributes'] ?? []),
            ...($properties['redacted'] ?? []),
        ]));
    }

    /**
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<=', now()->subMonths(config('masar.activity_log.retention_months')));
    }
}
