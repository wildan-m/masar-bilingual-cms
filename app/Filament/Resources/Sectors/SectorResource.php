<?php

namespace App\Filament\Resources\Sectors;

use App\Filament\Resources\Sectors\Pages\ManageSectors;
use App\Filament\Support\LocaleTabs;
use App\Filament\Support\LocalizedFields;
use App\Filament\Support\LocalizedTextColumn;
use App\Filament\Support\TranslationStatusColumn;
use App\Models\Sector;
use App\Support\Localization\Locale;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Market sectors: group projects on the website and qualify incoming RFPs.
 */
class SectorResource extends Resource
{
    protected static ?string $model = Sector::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Website content';

    protected static ?int $navigationSort = 31;

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->translate('name', 'en');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                LocaleTabs::make(fn (Locale $locale): array => [
                    LocalizedFields::title($locale, 'name', 'Name'),
                    LocalizedFields::slug($locale, 'projects?sector='),
                ]),
                TextInput::make('sort_order')
                    ->label('Order')
                    ->integer()
                    ->minValue(0)
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                LocalizedTextColumn::make('name', 'Name'),
                TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('Projects'),
                TranslationStatusColumn::make(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSectors::route('/'),
        ];
    }
}
