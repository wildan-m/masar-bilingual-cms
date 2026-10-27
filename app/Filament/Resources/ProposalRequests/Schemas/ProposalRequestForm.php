<?php

namespace App\Filament\Resources\ProposalRequests\Schemas;

use App\Filament\Resources\ProposalRequests\Actions\ProposalRequestActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Staff can only change the assignee and internal notes; what the client
 * submitted is read-only, and the status changes through workflow actions.
 */
class ProposalRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Handling')
                    ->schema([
                        Select::make('assigned_to')
                            ->label('Assignee')
                            ->options(fn (): array => ProposalRequestActions::assigneeOptions())
                            ->placeholder('Unassigned'),
                        Textarea::make('internal_notes')
                            ->label('Internal notes')
                            ->helperText('Visible to the proposals team only. Never shared with the client.')
                            ->rows(10)
                            ->maxLength(10000),
                    ]),
            ]);
    }
}
