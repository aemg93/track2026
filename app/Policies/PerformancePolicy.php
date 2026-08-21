<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Performance;

class PerformancePolicy
{
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Monitor']);
    }

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

    public function update(User $user, Performance $performance): bool
    {
        return $user->isSuperAdmin()
            || (($user->isAdmin() || $user->isMonitor())
                && $user->canAccessStudio($performance->studio_id));
    }

    public function delete(User $user, Performance $performance): bool
    {
        return $user->isSuperAdmin();
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
