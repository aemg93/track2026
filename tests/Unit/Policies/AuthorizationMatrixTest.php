<?php

namespace Tests\Unit\Policies;

use App\Models\Performance;
use App\Models\User;
use App\Policies\PerformancePolicy;
use Mockery;
use Tests\TestCase;

class AuthorizationMatrixTest extends TestCase
{
    public function test_admin_can_update_only_a_model_in_its_studio(): void
    {
        $admin = Mockery::mock(User::class);
        $admin->shouldReceive('isSuperAdmin')->andReturn(false);
        $admin->shouldReceive('isAdmin')->andReturn(true);
        $admin->shouldReceive('isMonitor')->andReturn(false);
        $admin->shouldReceive('canAccessStudio')->with(7)->andReturn(true);

        $performance = new Performance(['studio_id' => 7]);

        $this->assertTrue((new PerformancePolicy)->update($admin, $performance));
    }

    public function test_monitor_and_admin_cannot_delete_models(): void
    {
        foreach ([true, false] as $isAdmin) {
            $user = Mockery::mock(User::class);
            $user->shouldReceive('isSuperAdmin')->andReturn(false);
            $user->shouldReceive('isAdmin')->andReturn($isAdmin);
            $user->shouldReceive('isMonitor')->andReturn(! $isAdmin);

            $this->assertFalse((new PerformancePolicy)->delete($user, new Performance));
        }
    }

    public function test_only_super_admin_can_delete_models(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isSuperAdmin')->andReturn(true);

        $this->assertTrue((new PerformancePolicy)->delete($user, new Performance));
    }
}
