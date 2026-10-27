<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ManageUsers);
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission(Permission::ManageUsers);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::ManageUsers);
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission(Permission::ManageUsers);
    }

    /**
     * Admins cannot delete themselves, which would risk locking everyone out.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasPermission(Permission::ManageUsers) && ! $user->is($model);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission(Permission::ManageUsers);
    }

    public function replicate(User $user, User $model): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
