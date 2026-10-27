<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Filament\Support\LocalizedFields;
use App\Filament\Support\TranslationCallout;
use App\Models\NewsCategory;
use App\Support\Localization\Locale;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TranslationCallout::make(),

                LocaleTabs::make(fn (Locale $locale): array => [
                    LocalizedFields::title($locale),
                    LocalizedFields::slug($locale, 'news/'),
                    LocalizedFields::textarea($locale, 'excerpt', 'Excerpt', maxLength: 300)
                        ->required($locale->isFallback()),
                    LocalizedFields::richText($locale)
                        ->required($locale->isFallback()),
                ]),

                Section::make('Publishing')
                    ->columns(3)
                    ->schema([
                        Select::make('news_category_id')
                            ->label('Category')
                            ->options(fn (): array => NewsCategory::query()->ordered()->get()
                                ->mapWithKeys(fn (NewsCategory $category): array => [$category->getKey() => $category->translate('name', 'en')])
                                ->all()),
                        DateTimePicker::make('published_at')
                            ->label('Publication date')
                            ->seconds(false)
                            ->default(now())
                            ->helperText('A future date schedules the article.'),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->inline(false),
                        FileUpload::make('cover_image')
                            ->image()
                            ->disk('public')
                            ->directory('news')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(4096)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                LocalizedFields::seoSection(),
            ]);
    }
}
