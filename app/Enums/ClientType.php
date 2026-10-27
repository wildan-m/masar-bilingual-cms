<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClientType: string implements HasLabel
{
    case Government = 'government';
    case SemiGovernment = 'semi_government';
    case Private = 'private';
    case Developer = 'developer';

    /**
     * Visitor-facing label in the current locale.
     */
    public function label(): string
    {
        return __("site.client_types.{$this->value}");
    }

    public function getLabel(): string
    {
        return __("site.client_types.{$this->value}", locale: 'en');
    }
}
