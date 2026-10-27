<?php

namespace App\Filament\Support;

use App\Support\Localization\Locale;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/**
 * A translatable attribute in a table: English on the first line and the
 * Arabic translation underneath (right-to-left), searchable in both.
 */
final class LocalizedTextColumn
{
    public static function make(string $attribute, ?string $label = null): TextColumn
    {
        return TextColumn::make($attribute)
            ->label($label)
            ->state(fn (Model $record): string => $record->translate($attribute, Locale::fallback()))
            ->description(function (Model $record) use ($attribute): ?HtmlString {
                $arabic = $record->localizedText($attribute)->in(Locale::Arabic);

                return $arabic === null ? null : new HtmlString('<span dir="rtl" lang="ar">'.e($arabic).'</span>');
            })
            ->weight('medium')
            ->wrap()
            ->searchable(query: function (Builder $query, string $search) use ($attribute): Builder {
                return $query->where(function (Builder $query) use ($attribute, $search): void {
                    foreach (Locale::cases() as $locale) {
                        $query->orWhere("{$attribute}->{$locale->value}", 'like', "%{$search}%");
                    }
                });
            })
            ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy("{$attribute}->".Locale::fallback()->value, $direction));
    }
}
