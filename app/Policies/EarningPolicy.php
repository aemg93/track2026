<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Earning;

class EarningPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Monitor', 'Performance']);
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

        return false;
    }
}