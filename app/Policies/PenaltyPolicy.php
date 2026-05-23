<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Penalty;

class PenaltyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function view(User $user, Penalty $penalty): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin')) {
            return $penalty->performance->studio_id === $user->studio_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function update(User $user, Penalty $penalty): bool
    {
        return false;
    }

    public function delete(User $user, Penalty $penalty): bool
    {
        return $user->hasRole('Super Admin');
    }
}