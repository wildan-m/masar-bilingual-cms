<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Filament\Support\LocalizedTextColumn;
use App\Filament\Support\MissingTranslationFilter;
use App\Filament\Support\TranslationStatusColumn;
use App\Filament\Support\ViewOnSiteAction;
use App\Models\Article;
use App\Models\NewsCategory;
use App\Support\Localization\Locale;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('category'))
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->disk('public')
                    ->imageSize(56)
                    ->square(),
                LocalizedTextColumn::make('title', 'Title'),
                TextColumn::make('category')
                    ->state(fn (Article $record): ?string => $record->category?->translate('name', 'en'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->state(fn (Article $record): string => match (true) {
                        ! $record->is_published => 'Draft',
                        $record->published_at?->isFuture() ?? true => 'Scheduled',
                        default => 'Published',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Published' => 'success',
                        'Scheduled' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('Publication date')
                    ->date()
                    ->sortable(),
                TranslationStatusColumn::make(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('news_category_id')
                    ->label('Category')
                    ->options(fn (): array => NewsCategory::query()->ordered()->get()
                        ->mapWithKeys(fn (NewsCategory $category): array => [$category->getKey() => $category->translate('name', 'en')])
                        ->all()),
                SelectFilter::make('status')
                    ->options(['published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Draft'])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'published' => $query->published(),
                        'scheduled' => $query->where('is_published', true)->where('published_at', '>', now()),
                        'draft' => $query->where('is_published', false),
                        default => $query,
                    }),
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
