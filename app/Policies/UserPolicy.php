<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Roles que un Admin tiene permitido asignar/gestionar.
     */
    private const ADMIN_MANAGEABLE_ROLES = [
        'Monitor',
        'Performance',
    ];

    public function create(User $actor, ?string $targetRole = null): bool
    {
        if ($actor->hasRole('Super Admin')) {
            return true;
        }

        if ($actor->hasRole('Admin') && $actor->can('user.create')) {
            return $targetRole === null
                || in_array($targetRole, self::ADMIN_MANAGEABLE_ROLES, true);
        }

        return false;
    }

    public function update(User $actor, User $target): bool
    {
        if ($actor->hasRole('Super Admin')) {
            return true;
        }

        if ($actor->hasRole('Admin') && $actor->can('user.update')) {
            return $target->hasAnyRole(self::ADMIN_MANAGEABLE_ROLES);
        }

        return false;
    }

    public function delete(User $actor, User $target): bool
    {
        if ($actor->hasRole('Super Admin')) {
            return $actor->id !== $target->id;
        }

        if ($actor->hasRole('Admin') && $actor->can('user.delete')) {
            return $target->hasAnyRole(self::ADMIN_MANAGEABLE_ROLES);
        }

        return false;
    }
}