<?php

namespace App\Filament\Resources\ProposalRequests\Actions;

use App\Enums\Permission;
use App\Enums\ProposalRequestStatus;
use App\Models\ProposalRequest;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Actions shared by the RFP list and detail pages. Every action checks the
 * ProposalRequestPolicy, so editors never see them.
 */
final class ProposalRequestActions
{
    /**
     * One action per workflow step; only the steps allowed from the current
     * status are visible.
     *
     * @return list<Action>
     */
    public static function transitions(): array
    {
        return array_map(fn (ProposalRequestStatus $status): Action => Action::make("status_{$status->value}")
            ->label(fn (ProposalRequest $record): string => $status->actionLabel($record->status))
            ->icon($status->getIcon())
            ->color($status->getColor())
            ->authorize('update')
            ->visible(fn (ProposalRequest $record): bool => $record->status->canTransitionTo($status))
            ->requiresConfirmation()
            ->modalHeading(fn (ProposalRequest $record): string => "{$status->actionLabel($record->status)}: {$record->reference}")
            ->modalDescription(fn (ProposalRequest $record): string => "The request moves from \"{$record->status->getLabel()}\" to \"{$status->getLabel()}\".")
            ->action(function (ProposalRequest $record) use ($status): void {
                $record->transitionTo($status);

                Notification::make()
                    ->title("{$record->reference} is now \"{$status->getLabel()}\"")
                    ->success()
                    ->send();
            }), ProposalRequestStatus::cases());
    }

    public static function assign(): Action
    {
        return Action::make('assign')
            ->label(fn (ProposalRequest $record): string => $record->assigned_to ? 'Reassign' : 'Assign')
            ->icon(Heroicon::OutlinedUserPlus)
            ->color('gray')
            ->authorize('update')
            ->fillForm(fn (ProposalRequest $record): array => ['assigned_to' => $record->assigned_to])
            ->schema([
                Select::make('assigned_to')
                    ->label('Assignee')
                    ->options(fn (): array => self::assigneeOptions())
                    ->required(),
            ])
            ->action(function (ProposalRequest $record, array $data): void {
                $record->update(['assigned_to' => $data['assigned_to']]);

                Notification::make()->title('Assignee updated')->success()->send();
            });
    }

    public static function assignToMe(): Action
    {
        return Action::make('assign_to_me')
            ->label('Assign to me')
            ->icon(Heroicon::OutlinedHandRaised)
            ->color('gray')
            ->authorize('update')
            ->visible(fn (ProposalRequest $record): bool => $record->assigned_to === null)
            ->action(function (ProposalRequest $record): void {
                $record->update(['assigned_to' => auth()->id()]);

                Notification::make()->title("{$record->reference} assigned to you")->success()->send();
            });
    }

    public static function downloadAttachment(): Action
    {
        return Action::make('download_attachment')
            ->label('Download attachment')
            ->icon(Heroicon::OutlinedPaperClip)
            ->color('gray')
            ->visible(fn (ProposalRequest $record): bool => auth()->user()?->can('downloadAttachment', $record) ?? false)
            ->url(fn (ProposalRequest $record): string => route('admin.proposal-requests.attachment', $record));
    }

    /**
     * Active staff who can work on requests for proposal.
     *
     * @return array<int, string>
     */
    public static function assigneeOptions(): array
    {
        return User::query()
            ->active()
            ->withPermission(Permission::UpdateProposalRequests)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
