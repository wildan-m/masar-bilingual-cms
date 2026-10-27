<?php

namespace App\Filament\Resources\SitePages\Pages;

use App\Filament\Resources\SitePages\SitePageResource;
use App\Filament\Support\ViewOnSiteAction;
use App\Support\Localization\Locale;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSitePage extends EditRecord
{
    protected static string $resource = SitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewOnSiteAction::make(Locale::English),
            ViewOnSiteAction::make(Locale::Arabic),
            DeleteAction::make(),
        ];
    }
}
