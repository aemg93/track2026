<?php

namespace App\Policies;

use App\Models\Deduction;
use App\Models\User;

class DeductionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Monitor', 'Performance']);
    }

    public function view(User $user, Deduction $deduction): bool
    {
        $performance = $deduction->performance;
        return $user->isSuperAdmin()
            || ($user->isPerformance() && $performance?->user_id === $user->id)
            || (($user->isAdmin() || $user->isMonitor())
                && $performance?->studio_id === $user->studio_id);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Monitor']);
    }

    public function update(User $user, Deduction $deduction): bool
    {
        return $this->view($user, $deduction) && $user->hasAnyRole(['Super Admin', 'Admin', 'Monitor']);
    }
}
