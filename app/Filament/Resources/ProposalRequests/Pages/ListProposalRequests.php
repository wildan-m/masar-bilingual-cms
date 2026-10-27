<?php

namespace App\Filament\Resources\ProposalRequests\Pages;

use App\Enums\ProposalRequestStatus;
use App\Exports\ProposalRequestCsvExport;
use App\Filament\Resources\ProposalRequests\ProposalRequestResource;
use App\Models\ProposalRequest;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListProposalRequests extends ListRecords
{
    protected static string $resource = ProposalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->authorize('export')
                ->action(fn () => app(ProposalRequestCsvExport::class)->download($this->getFilteredSortedTableQuery())),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $counts = ProposalRequest::query()
            ->toBase()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $tabs = [
            'open' => Tab::make('Open')
                ->badge($counts->except(ProposalRequestStatus::Closed->value)->sum() ?: null)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->open()),
        ];

        foreach (ProposalRequestStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->badge($counts->get($status->value) ?: null)
                ->badgeColor($status->getColor())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', $status));
        }

        return [...$tabs, 'all' => Tab::make('All')];
    }
}
