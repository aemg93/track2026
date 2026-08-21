<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Earning;
use App\Models\Performance;

class EarningPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Monitor', 'Performance']);
    }

    public function view(User $user, Earning $earning): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Performance')) {
            return $earning->user_id === $user->id;
        }

        if ($user->hasRole('Monitor')) {
            return $earning->performance->studio_id === $user->studio_id;
        }

        if ($user->hasRole('Admin')) {
            return $earning->performance->studio_id === $user->studio_id;
        }

        return false;
    }

    public function create(User $user, Performance $performance): bool
    {
        return $user->isSuperAdmin()
            || (($user->isAdmin() || $user->isMonitor())
                && $user->canAccessStudio($performance->studio_id));
    }

    public function update(User $user, Earning $earning): bool
    {
        return $this->view($user, $earning)
            && $user->hasAnyRole(['Super Admin', 'Admin', 'Monitor']);
    }
}
