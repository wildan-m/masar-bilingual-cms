<?php

namespace App\Filament\Support;

use App\Support\Localization\Locale;
use App\Support\Localization\TranslationCompleteness;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Badge showing how complete a record's Arabic translation is:
 * "Complete", "3 of 5" or "Missing", with the untranslated fields in a tooltip.
 */
final class TranslationStatusColumn
{
    public static function make(Locale $locale = Locale::Arabic): TextColumn
    {
        $completeness = fn (Model $record): TranslationCompleteness => $record->translationCompleteness($locale);

        return TextColumn::make("translation_{$locale->value}")
            ->label($locale->englishName())
            ->badge()
            ->state(fn (Model $record): string => match (true) {
                $completeness($record)->isComplete() => 'Complete',
                $completeness($record)->isUntranslated() => 'Missing',
                default => "{$completeness($record)->filled()} of {$completeness($record)->total}",
            })
            ->color(fn (Model $record): string => match (true) {
                $completeness($record)->isComplete() => 'success',
                $completeness($record)->isUntranslated() => 'danger',
                default => 'warning',
            })
            ->icon(fn (Model $record): Heroicon => $completeness($record)->isComplete()
                ? Heroicon::OutlinedCheckCircle
                : Heroicon::OutlinedLanguage)
            ->tooltip(function (Model $record) use ($completeness): ?string {
                $missing = $completeness($record)->missing;

                return $missing === []
                    ? null
                    : 'Untranslated: '.collect($missing)->map(fn (string $attribute): string => Str::headline($attribute))->join(', ');
            });
    }
}
