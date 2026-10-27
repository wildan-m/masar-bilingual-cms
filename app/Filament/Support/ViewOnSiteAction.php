<?php

namespace App\Filament\Support;

use App\Support\Localization\Locale;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

/**
 * Opens the public page of a record in the given language.
 */
final class ViewOnSiteAction
{
    public static function make(Locale $locale = Locale::English): Action
    {
        return Action::make("view_on_site_{$locale->value}")
            ->label($locale->isFallback() ? 'View on site' : "View in {$locale->englishName()}")
            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
            ->color('gray')
            ->url(fn (Model $record): string => $record->url($locale))
            ->openUrlInNewTab()
            ->visible(fn (Model $record): bool => $record->exists && $record->isPublished());
    }
}
