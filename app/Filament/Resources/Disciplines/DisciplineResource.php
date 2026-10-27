<?php

namespace App\Filament\Resources\Disciplines;

use App\Filament\Resources\Disciplines\Pages\CreateDiscipline;
use App\Filament\Resources\Disciplines\Pages\EditDiscipline;
use App\Filament\Resources\Disciplines\Pages\ListDisciplines;
use App\Filament\Resources\Disciplines\Schemas\DisciplineForm;
use App\Filament\Resources\Disciplines\Tables\DisciplinesTable;
use App\Models\Discipline;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class DisciplineResource extends Resource
{
    protected static ?string $model = Discipline::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Website content';

    protected static ?int $navigationSort = 20;

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->translate('name', 'en');
    }

    public static function form(Schema $schema): Schema
    {
        return DisciplineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisciplinesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDisciplines::route('/'),
            'create' => CreateDiscipline::route('/create'),
            'edit' => EditDiscipline::route('/{record}/edit'),
        ];
    }
}
