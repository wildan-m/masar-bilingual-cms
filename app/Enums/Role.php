<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasColor, HasDescription, HasLabel
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Intake = 'intake';

    /**
     * @return list<Permission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin => Permission::cases(),
            self::Editor => [
                Permission::ManageContent,
                Permission::ManageSettings,
            ],
            self::Intake => [
                Permission::ViewProposalRequests,
                Permission::UpdateProposalRequests,
                Permission::DownloadProposalAttachments,
                Permission::ExportProposalRequests,
            ],
        };
    }

    public function allows(Permission $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Editor => 'Content editor',
            self::Intake => 'RFP intake',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Admin => 'Full access, including users, settings and the activity log.',
            self::Editor => 'Edits website content and settings. No access to RFPs or users.',
            self::Intake => 'Handles incoming requests for proposal. Cannot edit website content.',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Editor => 'info',
            self::Intake => 'warning',
        };
    }
}
