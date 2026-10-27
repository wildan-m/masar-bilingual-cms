<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum PageTemplate: string implements HasDescription, HasLabel
{
    case Standard = 'standard';
    case Contact = 'contact';

    public function getLabel(): string
    {
        return match ($this) {
            self::Standard => 'Standard page',
            self::Contact => 'Contact page with RFP form',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Standard => 'Title and rich text body.',
            self::Contact => 'Body text followed by the contact details and the request-for-proposal form.',
        };
    }
}
