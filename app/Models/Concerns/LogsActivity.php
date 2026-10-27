<?php

namespace App\Models\Concerns;

use App\Enums\ActivityEvent;
use App\Support\Activity\ActivityLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Records created, updated and deleted events in the activity log.
 *
 * Models may declare:
 *  - `$activityIgnored`: attributes that are bookkeeping noise (never logged);
 *  - `$activitySensitive`: personal or secret attributes. The log notes that
 *    they changed but never stores their values.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        // Registered after the model's own booted() listeners, so values they
        // set while saving (such as generated references) are part of the log.
        static::whenBooted(function (): void {
            static::created(function (Model $model): void {
                ActivityLogger::log(ActivityEvent::Created, $model, $model->activitySnapshot());
            });

            static::updated(function (Model $model): void {
                $changes = $model->activityChanges();

                if ($changes !== null) {
                    ActivityLogger::log(ActivityEvent::Updated, $model, $changes);
                }
            });

            static::deleted(function (Model $model): void {
                ActivityLogger::log(ActivityEvent::Deleted, $model, $model->activitySnapshot());
            });
        });
    }

    /**
     * @return list<string>
     */
    public function activityIgnoredAttributes(): array
    {
        return [
            'created_at',
            'updated_at',
            ...(property_exists($this, 'activityIgnored') ? $this->activityIgnored : []),
        ];
    }

    /**
     * @return list<string>
     */
    public function activitySensitiveAttributes(): array
    {
        return [
            'password',
            'remember_token',
            ...(property_exists($this, 'activitySensitive') ? $this->activitySensitive : []),
        ];
    }

    public function activityLabel(): string
    {
        foreach (['title', 'name'] as $attribute) {
            if (method_exists($this, 'localizedAttributes') && in_array($attribute, $this->localizedAttributes(), true)) {
                return $this->translate($attribute, 'en');
            }

            if (is_string($this->getAttribute($attribute))) {
                return $this->getAttribute($attribute);
            }
        }

        return class_basename($this).' #'.$this->getKey();
    }

    /**
     * @return array<string, mixed>
     */
    public function activitySnapshot(): array
    {
        $attributes = Arr::except($this->getAttributes(), [...$this->activityIgnoredAttributes(), $this->getKeyName()]);

        $values = [];
        $redacted = [];

        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->activitySensitiveAttributes(), true)) {
                if ($value !== null) {
                    $redacted[] = $key;
                }

                continue;
            }

            $values[$key] = $this->activityValue($key, $value);
        }

        return array_filter(['attributes' => $values, 'redacted' => $redacted]);
    }

    /**
     * The attributes changed by the last save, or null when only ignored
     * attributes changed.
     *
     * @return array<string, mixed>|null
     */
    public function activityChanges(): ?array
    {
        $changes = Arr::except($this->getChanges(), $this->activityIgnoredAttributes());

        if ($changes === []) {
            return null;
        }

        $old = [];
        $new = [];
        $redacted = [];

        foreach ($changes as $key => $value) {
            if (in_array($key, $this->activitySensitiveAttributes(), true)) {
                $redacted[] = $key;

                continue;
            }

            $old[$key] = $this->activityValue($key, $this->getRawOriginal($key));
            $new[$key] = $this->activityValue($key, $value);
        }

        return array_filter(['old' => $old, 'new' => $new, 'redacted' => $redacted]);
    }

    /**
     * Localized attributes are stored as JSON strings; log them as structured
     * translations instead of escaped JSON.
     */
    protected function activityValue(string $key, mixed $value): mixed
    {
        $isLocalized = method_exists($this, 'localizedAttributes')
            && in_array($key, $this->localizedAttributes(), true);

        if ($isLocalized && is_string($value)) {
            return json_decode($value, true) ?? $value;
        }

        return $value;
    }
}
