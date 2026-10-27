<?php

namespace App\Filament\Resources\SitePages\Tables;

use App\Filament\Support\LocalizedTextColumn;
use App\Filament\Support\MissingTranslationFilter;
use App\Filament\Support\TranslationStatusColumn;
use App\Filament\Support\ViewOnSiteAction;
use App\Models\Page;
use App\Support\Localization\Locale;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SitePagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                LocalizedTextColumn::make('title', 'Title'),
                TextColumn::make('template')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('key')
                    ->label('Navigation')
                    ->state(fn (Page $record): ?string => $record->isSystemPage() ? 'System page' : null)
                    ->badge()
                    ->color('info'),
                TranslationStatusColumn::make(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('id')
            ->filters([
                TernaryFilter::make('is_published')->label('Published'),
                MissingTranslationFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    ViewOnSiteAction::make(Locale::English),
                    ViewOnSiteAction::make(Locale::Arabic),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->authorizeIndividualRecords('delete'),
                ]),
            ]);
    }
}
