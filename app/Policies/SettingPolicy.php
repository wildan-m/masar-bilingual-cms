<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class SettingPolicy
{
    public function manage(User $user): bool
    {
        return $user->hasPermission(Permission::ManageSettings);
    }
}
