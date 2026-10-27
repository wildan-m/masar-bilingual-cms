<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\ActivityLog;
use App\Models\User;

/**
 * The activity log is read-only for everyone.
 */
class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ViewActivityLog);
    }

    public function view(User $user, ActivityLog $activityLog): bool
    {
        return $user->hasPermission(Permission::ViewActivityLog);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }

    public function delete(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    public function restore(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
