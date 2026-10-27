<?php

namespace App\Filament\Support;

use App\Support\Localization\Locale;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

final class MissingTranslationFilter
{
    public static function make(Locale $locale = Locale::Arabic): TernaryFilter
    {
        return TernaryFilter::make("{$locale->value}_translation")
            ->label("{$locale->englishName()} translation")
            ->placeholder('All records')
            ->trueLabel('Complete')
            ->falseLabel('Incomplete')
            ->queries(
                true: fn (Builder $query): Builder => $query->fullyTranslated($locale),
                false: fn (Builder $query): Builder => $query->missingTranslation($locale),
                blank: fn (Builder $query): Builder => $query,
            );
    }
}
