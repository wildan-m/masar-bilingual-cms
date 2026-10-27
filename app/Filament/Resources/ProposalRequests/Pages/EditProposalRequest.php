<?php

namespace App\Filament\Resources\ProposalRequests\Pages;

use App\Filament\Resources\ProposalRequests\ProposalRequestResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProposalRequest extends EditRecord
{
    protected static string $resource = ProposalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
