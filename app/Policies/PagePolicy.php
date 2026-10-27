<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PagePolicy extends ContentPolicy
{
    /**
     * System pages (about, contact, privacy) are linked from the navigation
     * and cannot be deleted; they can be unpublished instead.
     */
    public function delete(User $user, Model $model): bool
    {
        return $model instanceof Page
            && ! $model->isSystemPage()
            && parent::delete($user, $model);
    }
}
