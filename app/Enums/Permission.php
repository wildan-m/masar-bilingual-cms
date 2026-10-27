<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Fine-grained abilities granted to roles. Policies check permissions rather
 * than role names, so changing what a role may do happens in Role only.
 */
enum Permission: string implements HasLabel
{
    case ManageContent = 'manage_content';
    case ManageSettings = 'manage_settings';
    case ViewProposalRequests = 'view_proposal_requests';
    case UpdateProposalRequests = 'update_proposal_requests';
    case DownloadProposalAttachments = 'download_proposal_attachments';
    case ExportProposalRequests = 'export_proposal_requests';
    case DeleteProposalRequests = 'delete_proposal_requests';
    case ManageUsers = 'manage_users';
    case ViewActivityLog = 'view_activity_log';

    public function getLabel(): string
    {
        return match ($this) {
            self::ManageContent => 'Manage pages, disciplines, projects and news',
            self::ManageSettings => 'Edit site settings',
            self::ViewProposalRequests => 'View requests for proposal',
            self::UpdateProposalRequests => 'Assign, annotate and move RFPs through the workflow',
            self::DownloadProposalAttachments => 'Download RFP attachments',
            self::ExportProposalRequests => 'Export RFPs to CSV',
            self::DeleteProposalRequests => 'Delete RFPs',
            self::ManageUsers => 'Manage users and roles',
            self::ViewActivityLog => 'View the activity log',
        };
    }
}
