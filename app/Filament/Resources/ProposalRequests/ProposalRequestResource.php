<?php

namespace App\Filament\Resources\ProposalRequests;

use App\Enums\ProposalRequestStatus;
use App\Filament\Resources\ProposalRequests\Pages\EditProposalRequest;
use App\Filament\Resources\ProposalRequests\Pages\ListProposalRequests;
use App\Filament\Resources\ProposalRequests\Pages\ViewProposalRequest;
use App\Filament\Resources\ProposalRequests\Schemas\ProposalRequestForm;
use App\Filament\Resources\ProposalRequests\Schemas\ProposalRequestInfolist;
use App\Filament\Resources\ProposalRequests\Tables\ProposalRequestsTable;
use App\Models\ProposalRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProposalRequestResource extends Resource
{
    protected static ?string $model = ProposalRequest::class;

    protected static ?string $modelLabel = 'request for proposal';

    protected static ?string $pluralModelLabel = 'requests for proposal';

    protected static ?string $navigationLabel = 'Requests for proposal';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Enquiries';

    protected static ?int $navigationSort = 10;

    public static function getTitleCaseModelLabel(): string
    {
        return 'Request for proposal';
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return 'Requests for proposal';
    }

    /**
     * Number of new requests waiting to be reviewed.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = ProposalRequest::query()->where('status', ProposalRequestStatus::New)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'info';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'name', 'company', 'email'];
    }

    public static function form(Schema $schema): Schema
    {
        return ProposalRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProposalRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProposalRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProposalRequests::route('/'),
            'view' => ViewProposalRequest::route('/{record}'),
            'edit' => EditProposalRequest::route('/{record}/edit'),
        ];
    }
}
