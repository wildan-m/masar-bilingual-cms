<?php

namespace App\Support\Activity;

use App\Enums\ActivityEvent;
use App\Enums\ProposalRequestStatus;
use App\Models\ActivityLog;
use Illuminate\Support\Str;

/**
 * Turns the properties of an activity log entry into readable rows for the
 * admin: one row per attribute with its previous and new value.
 */
final class ActivityChanges
{
    /**
     * @return list<array{attribute: string, old: ?string, new: ?string}>
     */
    public static function rows(ActivityLog $log): array
    {
        $properties = $log->properties ?? [];

        if ($log->event === ActivityEvent::StatusChanged) {
            return [[
                'attribute' => 'Status',
                'old' => self::status($properties['from'] ?? null),
                'new' => self::status($properties['to'] ?? null),
            ]];
        }

        if ($log->event === ActivityEvent::Exported) {
            return [[
                'attribute' => 'Rows exported',
                'old' => null,
                'new' => isset($properties['count']) ? (string) $properties['count'] : null,
            ]];
        }

        $rows = [];

        if (isset($properties['new'])) {
            foreach ($properties['new'] as $attribute => $value) {
                $rows[] = [
                    'attribute' => Str::headline($attribute),
                    'old' => self::format($properties['old'][$attribute] ?? null),
                    'new' => self::format($value),
                ];
            }
        }

        foreach ($properties['attributes'] ?? [] as $attribute => $value) {
            $formatted = self::format($value);

            $rows[] = $log->event === ActivityEvent::Deleted
                ? ['attribute' => Str::headline($attribute), 'old' => $formatted, 'new' => null]
                : ['attribute' => Str::headline($attribute), 'old' => null, 'new' => $formatted];
        }

        foreach ($properties['redacted'] ?? [] as $attribute) {
            $rows[] = ['attribute' => Str::headline($attribute), 'old' => '(hidden)', 'new' => '(hidden)'];
        }

        return $rows;
    }

    private static function status(?string $value): ?string
    {
        return $value === null ? null : (ProposalRequestStatus::tryFrom($value)?->getLabel() ?? $value);
    }

    private static function format(mixed $value): ?string
    {
        return match (true) {
            $value === null => null,
            is_bool($value) => $value ? 'Yes' : 'No',
            // Translations are stored per locale: show one line per language.
            is_array($value) => collect($value)
                ->map(fn (mixed $text, string|int $key): string => strtoupper((string) $key).': '.self::plain(
                    is_scalar($text) ? (string) $text : (string) json_encode($text, JSON_UNESCAPED_UNICODE),
                ))
                ->join("\n"),
            default => self::plain((string) $value),
        };
    }

    private static function plain(string $value): string
    {
        return Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags($value)) ?? ''), 240);
    }
}
