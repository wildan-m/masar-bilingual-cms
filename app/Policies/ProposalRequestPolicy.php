<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\ProposalRequest;
use App\Models\User;

/**
 * Requests for proposal are visible to intake staff and admins only.
 * They are created exclusively through the public form.
 */
class ProposalRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ViewProposalRequests);
    }

    public function view(User $user, ProposalRequest $proposalRequest): bool
    {
        return $user->hasPermission(Permission::ViewProposalRequests);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ProposalRequest $proposalRequest): bool
    {
        return $user->hasPermission(Permission::UpdateProposalRequests);
    }

    public function downloadAttachment(User $user, ProposalRequest $proposalRequest): bool
    {
        return $proposalRequest->hasAttachment()
            && $user->hasPermission(Permission::DownloadProposalAttachments);
    }

    public function export(User $user): bool
    {
        return $user->hasPermission(Permission::ExportProposalRequests);
    }

    public function delete(User $user, ProposalRequest $proposalRequest): bool
    {
        return $user->hasPermission(Permission::DeleteProposalRequests);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission(Permission::DeleteProposalRequests);
    }

    public function replicate(User $user, ProposalRequest $proposalRequest): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    public function restore(User $user, ProposalRequest $proposalRequest): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, ProposalRequest $proposalRequest): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
