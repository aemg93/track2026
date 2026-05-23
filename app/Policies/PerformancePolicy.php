<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Performance;

class PerformancePolicy
{
    public function view(User $user, Performance $performance): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isPerformance()) {
            return $this->canViewOwn($user, $performance);
        }

        if ($this->canViewStudio($user)) {
            return $user->canAccessStudio($performance->studio_id);
        }

        return false;
    }

    public function viewAny(User $user): bool
    {
        return $user->canAccessPerformances();
    }

    private function canViewOwn(User $user, Performance $performance): bool
    {
        return $user->id === $performance->user_id;
    }

    private function canViewStudio(User $user): bool
    {
        return $user->isAdmin() || $user->isMonitor();
    }
}