<?php

namespace App\Filament\Resources\SitePages;

use App\Filament\Resources\SitePages\Pages\CreateSitePage;
use App\Filament\Resources\SitePages\Pages\EditSitePage;
use App\Filament\Resources\SitePages\Pages\ListSitePages;
use App\Filament\Resources\SitePages\Schemas\SitePageForm;
use App\Filament\Resources\SitePages\Tables\SitePagesTable;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * CMS pages (about, contact, privacy and any page editors add).
 */
class SitePageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $modelLabel = 'page';

    protected static ?string $slug = 'pages';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Website content';

    protected static ?int $navigationSort = 10;

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->translate('title', 'en');
    }

    public static function form(Schema $schema): Schema
    {
        return SitePageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SitePagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSitePages::route('/'),
            'create' => CreateSitePage::route('/create'),
            'edit' => EditSitePage::route('/{record}/edit'),
        ];
    }
}
