<?php

namespace App\Policies;

use App\Models\Studio;
use App\Models\User;

class StudioPolicy
{
    public function view(User $user, Studio $studio): bool
    {
        return $user->isSuperAdmin() || $user->canAccessStudio($studio->id);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Monitor']);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
