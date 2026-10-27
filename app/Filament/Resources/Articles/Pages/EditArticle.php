<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Support\ViewOnSiteAction;
use App\Support\Localization\Locale;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewOnSiteAction::make(Locale::English),
            ViewOnSiteAction::make(Locale::Arabic),
            DeleteAction::make(),
        ];
    }
}
