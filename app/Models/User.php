<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function performance()
    {
        return $this->hasOne(Performance::class);
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isMonitor(): bool
    {
        return $this->hasRole('Monitor');
    }

    public function isPerformance(): bool
    {
        return $this->hasRole('Performance');
    }

    public function canAccessStudio(int $studioId): bool
    {
        return isset($this->studio_id)
            && $this->studio_id === $studioId;
    }

    public function canAccessPerformances(): bool
    {
        return $this->hasAnyRole([
            'Super Admin',
            'Admin',
            'Monitor',
            'Performance'
        ]);
    }
}