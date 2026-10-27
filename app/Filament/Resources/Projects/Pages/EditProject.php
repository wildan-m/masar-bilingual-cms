<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Support\ViewOnSiteAction;
use App\Support\Localization\Locale;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewOnSiteAction::make(Locale::English),
            ViewOnSiteAction::make(Locale::Arabic),
            DeleteAction::make(),
        ];
    }
}
