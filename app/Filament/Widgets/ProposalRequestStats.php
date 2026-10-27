<?php

namespace App\Filament\Widgets;

use App\Enums\ProposalRequestStatus;
use App\Models\ProposalRequest;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Gate;

class ProposalRequestStats extends StatsOverviewWidget
{
    protected static ?int $sort = 10;

    protected ?string $heading = 'Requests for proposal';

    public static function canView(): bool
    {
        return Gate::allows('viewAny', ProposalRequest::class);
    }

    protected function getStats(): array
    {
        $count = fn (ProposalRequestStatus $status): int => ProposalRequest::query()->where('status', $status)->count();
        $unassigned = ProposalRequest::query()->open()->whereNull('assigned_to')->count();

        return [
            Stat::make('New', $count(ProposalRequestStatus::New))
                ->description($unassigned > 0 ? "{$unassigned} open and unassigned" : 'Every open request has an owner')
                ->descriptionIcon($unassigned > 0 ? Heroicon::OutlinedExclamationTriangle : Heroicon::OutlinedCheckCircle)
                ->color($unassigned > 0 ? 'warning' : 'success'),
            Stat::make('In review', $count(ProposalRequestStatus::InReview))
                ->color('warning'),
            Stat::make('Proposals sent', $count(ProposalRequestStatus::ProposalSent))
                ->color('success'),
            Stat::make('Received in the last 30 days', ProposalRequest::query()->where('created_at', '>=', now()->subDays(30))->count()),
        ];
    }
}
