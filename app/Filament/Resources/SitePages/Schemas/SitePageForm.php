<?php

namespace App\Filament\Resources\SitePages\Schemas;

use App\Enums\PageTemplate;
use App\Filament\Support\LocaleTabs;
use App\Filament\Support\LocalizedFields;
use App\Filament\Support\TranslationCallout;
use App\Models\Page;
use App\Support\Localization\Locale;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SitePageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TranslationCallout::make(),

                LocaleTabs::make(fn (Locale $locale): array => [
                    LocalizedFields::title($locale),
                    LocalizedFields::slug($locale, ''),
                    LocalizedFields::richText($locale)
                        ->required($locale->isFallback()),
                ]),

                Section::make('Page settings')
                    ->columns(2)
                    ->description(fn (?Page $record): ?string => $record?->isSystemPage()
                        ? 'This page is linked from the site navigation. It can be unpublished but not deleted.'
                        : null)
                    ->schema([
                        Select::make('template')
                            ->options(PageTemplate::class)
                            ->default(PageTemplate::Standard)
                            ->required(),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->inline(false),
                    ]),

                LocalizedFields::seoSection(),
            ]);
    }
}
