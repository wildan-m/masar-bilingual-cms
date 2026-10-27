<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ActivityEvent: string implements HasColor, HasLabel
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case StatusChanged = 'status_changed';
    case AttachmentDownloaded = 'attachment_downloaded';
    case Exported = 'exported';

    public function getLabel(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Updated => 'Updated',
            self::Deleted => 'Deleted',
            self::StatusChanged => 'Status changed',
            self::AttachmentDownloaded => 'Attachment downloaded',
            self::Exported => 'Exported',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Created => 'success',
            self::Updated => 'info',
            self::Deleted => 'danger',
            self::StatusChanged => 'warning',
            self::AttachmentDownloaded, self::Exported => 'gray',
        };
    }
}
