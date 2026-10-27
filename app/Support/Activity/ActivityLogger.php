<?php

namespace App\Support\Activity;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Writes entries to the activity log.
 *
 * Model changes are recorded automatically by the LogsActivity trait; other
 * auditable actions (exports, attachment downloads) call log() directly.
 */
final class ActivityLogger
{
    private static bool $enabled = true;

    /**
     * @param  array<string, mixed>  $properties
     * @param  Model|class-string<Model>|null  $subject  A record, or a model class for actions on many records.
     */
    public static function log(
        ActivityEvent $event,
        Model|string|null $subject = null,
        array $properties = [],
        ?Authenticatable $causer = null,
    ): ?ActivityLog {
        if (! self::$enabled) {
            return null;
        }

        $causer ??= Auth::user();

        return ActivityLog::create([
            'user_id' => $causer?->getAuthIdentifier(),
            'event' => $event,
            'subject_type' => match (true) {
                $subject instanceof Model => $subject->getMorphClass(),
                is_string($subject) => (new $subject)->getMorphClass(),
                default => null,
            },
            'subject_id' => $subject instanceof Model ? $subject->getKey() : null,
            'subject_label' => $subject instanceof Model ? self::labelFor($subject) : null,
            'properties' => $properties === [] ? null : $properties,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
        ]);
    }

    /**
     * Run a callback without recording activity, e.g. for bulk imports.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function withoutLogging(callable $callback): mixed
    {
        $previous = self::$enabled;
        self::$enabled = false;

        try {
            return $callback();
        } finally {
            self::$enabled = $previous;
        }
    }

    public static function isEnabled(): bool
    {
        return self::$enabled;
    }

    private static function labelFor(Model $subject): string
    {
        $label = method_exists($subject, 'activityLabel') ? $subject->activityLabel() : null;

        return filled($label) ? (string) $label : class_basename($subject).' #'.$subject->getKey();
    }
}
