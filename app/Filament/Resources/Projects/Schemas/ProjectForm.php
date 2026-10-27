<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ClientType;
use App\Filament\Support\LocaleTabs;
use App\Filament\Support\LocalizedFields;
use App\Filament\Support\TranslationCallout;
use App\Models\Discipline;
use App\Models\Sector;
use App\Support\Localization\Locale;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TranslationCallout::make(),

                LocaleTabs::make(fn (Locale $locale): array => [
                    LocalizedFields::title($locale),
                    LocalizedFields::slug($locale, 'projects/'),
                    TextInput::make("location.{$locale->value}")
                        ->label('Location')
                        ->required($locale->isFallback())
                        ->maxLength(120),
                    LocalizedFields::textarea($locale, 'summary', 'Summary', maxLength: 300)
                        ->required($locale->isFallback()),
                    LocalizedFields::richText($locale)
                        ->required($locale->isFallback()),
                ]),

                Section::make('Project details')
                    ->columns(2)
                    ->schema([
                        Select::make('sector_id')
                            ->label('Sector')
                            ->options(fn (): array => Sector::query()->ordered()->get()
                                ->mapWithKeys(fn (Sector $sector): array => [$sector->getKey() => $sector->translate('name', 'en')])
                                ->all())
                            ->required(),
                        Select::make('client_type')
                            ->options(ClientType::class)
                            ->required(),
                        TextInput::make('completion_year')
                            ->integer()
                            ->minValue(1980)
                            ->maxValue((int) now()->addYears(10)->format('Y'))
                            ->helperText('Leave empty while the project is ongoing.'),
                        Select::make('disciplines')
                            ->relationship('disciplines', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Discipline $discipline): string => $discipline->translate('name', 'en'))
                            ->multiple()
                            ->preload(),
                    ]),

                Section::make('Media and publishing')
                    ->columns(3)
                    ->schema([
                        FileUpload::make('cover_image')
                            ->image()
                            ->disk('public')
                            ->directory('projects')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(4096)
                            ->imageEditor()
                            ->columnSpan(2),
                        Section::make()
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(false),
                                Toggle::make('is_featured')
                                    ->label('Featured on the home page')
                                    ->default(false),
                            ]),
                    ]),

                LocalizedFields::seoSection(),
            ]);
    }
}
