<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ClientType;
use App\Filament\Support\LocalizedTextColumn;
use App\Filament\Support\MissingTranslationFilter;
use App\Filament\Support\TranslationStatusColumn;
use App\Filament\Support\ViewOnSiteAction;
use App\Models\Project;
use App\Models\Sector;
use App\Support\Localization\Locale;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('sector'))
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->disk('public')
                    ->imageSize(56)
                    ->square(),
                LocalizedTextColumn::make('title', 'Title'),
                TextColumn::make('sector')
                    ->state(fn (Project $record): ?string => $record->sector?->translate('name', 'en'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('completion_year')
                    ->label('Completed')
                    ->placeholder('Ongoing')
                    ->sortable(),
                TextColumn::make('client_type')
                    ->label('Client')
                    ->toggleable(isToggledHiddenByDefault: true),
                TranslationStatusColumn::make(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
            ])
            ->defaultSort('completion_year', 'desc')
            ->filters([
                SelectFilter::make('sector_id')
                    ->label('Sector')
                    ->options(fn (): array => Sector::query()->ordered()->get()
                        ->mapWithKeys(fn (Sector $sector): array => [$sector->getKey() => $sector->translate('name', 'en')])
                        ->all()),
                SelectFilter::make('client_type')
                    ->options(ClientType::class),
                TernaryFilter::make('is_published')->label('Published'),
                TernaryFilter::make('is_featured')->label('Featured'),
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
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
