<?php

namespace App\Filament\Support;

use App\Support\Localization\Locale;
use Filament\Schemas\Components\Callout;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Warns editors at the top of a form when the saved record is not fully
 * translated, listing what visitors currently see in English.
 */
final class TranslationCallout
{
    public static function make(Locale $locale = Locale::Arabic): Callout
    {
        return Callout::make("{$locale->englishName()} translation incomplete")
            ->description(function (?Model $record) use ($locale): string {
                $missing = collect($record?->translationCompleteness($locale)->missing ?? [])
                    ->map(fn (string $attribute): string => Str::lower(Str::headline($attribute)))
                    ->join(', ', ' and ');

                return "The {$locale->englishName()} site shows the English {$missing} until a translation is added.";
            })
            ->icon(Heroicon::OutlinedLanguage)
            ->warning()
            ->columnSpanFull()
            ->visible(fn (?Model $record): bool => $record?->exists === true
                && ! $record->translationCompleteness($locale)->isComplete());
    }
}
