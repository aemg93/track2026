<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Bonus;

class BonusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function view(User $user, Bonus $bonus): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin')) {
            return $bonus->performance->studio_id === $user->studio_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function update(User $user, Bonus $bonus): bool
    {
        return false;
    }

    public function delete(User $user, Bonus $bonus): bool
    {
        return $user->hasRole('Super Admin');
    }
}