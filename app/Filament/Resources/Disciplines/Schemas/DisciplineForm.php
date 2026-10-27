<?php

namespace App\Filament\Resources\Disciplines\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Filament\Support\LocalizedFields;
use App\Filament\Support\TranslationCallout;
use App\Models\Discipline;
use App\Support\Localization\Locale;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DisciplineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TranslationCallout::make(),

                LocaleTabs::make(fn (Locale $locale): array => [
                    LocalizedFields::title($locale, 'name', 'Name'),
                    LocalizedFields::slug($locale, 'disciplines/'),
                    LocalizedFields::textarea($locale, 'summary', 'Summary', maxLength: 300)
                        ->required($locale->isFallback()),
                    LocalizedFields::richText($locale)
                        ->required($locale->isFallback()),
                ]),

                Section::make('Display')
                    ->columns(3)
                    ->schema([
                        Select::make('icon')
                            ->options(Discipline::ICONS)
                            ->required()
                            ->native(false),
                        TextInput::make('sort_order')
                            ->label('Order')
                            ->integer()
                            ->minValue(0)
                            ->default(0),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->inline(false),
                    ]),

                LocalizedFields::seoSection(),
            ]);
    }
}
