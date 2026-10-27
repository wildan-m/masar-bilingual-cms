<?php

namespace App\Filament\Resources\ProposalRequests\Pages;

use App\Filament\Resources\ProposalRequests\Actions\ProposalRequestActions;
use App\Filament\Resources\ProposalRequests\ProposalRequestResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProposalRequest extends ViewRecord
{
    protected static string $resource = ProposalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...ProposalRequestActions::transitions(),
            ProposalRequestActions::assign(),
            ProposalRequestActions::downloadAttachment(),
            ActionGroup::make([
                EditAction::make()->label('Edit notes'),
                DeleteAction::make(),
            ]),
        ];
    }
}
