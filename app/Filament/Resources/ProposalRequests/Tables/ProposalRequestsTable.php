<?php

namespace App\Filament\Resources\ProposalRequests\Tables;

use App\Enums\ProposalRequestStatus;
use App\Exports\ProposalRequestCsvExport;
use App\Filament\Resources\ProposalRequests\Actions\ProposalRequestActions;
use App\Models\ProposalRequest;
use App\Models\Sector;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProposalRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['sector', 'assignee']))
            ->columns([
                TextColumn::make('reference')
                    ->weight('medium')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Client')
                    ->description(fn (ProposalRequest $record): ?string => $record->company)
                    ->searchable(['name', 'company', 'email']),
                TextColumn::make('sector')
                    ->state(fn (ProposalRequest $record): ?string => $record->sector?->translate('name', 'en'))
                    ->placeholder('Not specified')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assigned to')
                    ->placeholder('Unassigned'),
                IconColumn::make('has_attachment')
                    ->label('File')
                    ->state(fn (ProposalRequest $record): bool => $record->hasAttachment())
                    ->icon(fn (bool $state): ?Heroicon => $state ? Heroicon::OutlinedPaperClip : null)
                    ->color('gray'),
                TextColumn::make('locale')
                    ->label('Language')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(ProposalRequestStatus::class)
                    ->multiple(),
                SelectFilter::make('sector_id')
                    ->label('Sector')
                    ->options(fn (): array => Sector::query()->ordered()->get()
                        ->mapWithKeys(fn (Sector $sector): array => [$sector->getKey() => $sector->translate('name', 'en')])
                        ->all()),
                SelectFilter::make('assignment')
                    ->options(['mine' => 'Assigned to me', 'unassigned' => 'Unassigned'])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'mine' => $query->where('assigned_to', auth()->id()),
                        'unassigned' => $query->whereNull('assigned_to'),
                        default => $query,
                    }),
                TernaryFilter::make('has_attachment')
                    ->label('Attachment')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('attachment_path'),
                        false: fn (Builder $query): Builder => $query->whereNull('attachment_path'),
                    ),
            ])
            // Rows open the request; "assign to me" is the only inline shortcut.
            ->recordActions([
                ProposalRequestActions::assignToMe()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export_selected')
                        ->label('Export selected to CSV')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->authorize('export')
                        ->action(fn (Collection $records) => app(ProposalRequestCsvExport::class)->download(
                            ProposalRequest::query()->whereKey($records->modelKeys())->latest(),
                        )),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
