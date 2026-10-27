<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Website content (pages, disciplines, projects, news and their taxonomies)
 * is managed by users with the ManageContent permission: editors and admins.
 *
 * Every ability Filament may check is defined explicitly; the admin panel
 * runs in strict authorization mode and fails loudly on a missing method.
 */
abstract class ContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ManageContent);
    }

    public function view(User $user, Model $model): bool
    {
        return $user->hasPermission(Permission::ManageContent);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::ManageContent);
    }

    public function update(User $user, Model $model): bool
    {
        return $user->hasPermission(Permission::ManageContent);
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->hasPermission(Permission::ManageContent);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission(Permission::ManageContent);
    }

    public function reorder(User $user): bool
    {
        return $user->hasPermission(Permission::ManageContent);
    }

    public function replicate(User $user, Model $model): bool
    {
        return false;
    }

    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
