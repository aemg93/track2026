<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);

        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'activity.create', 'activity.view', 'earnings.view.model', 'earnings.view.studio',
            'earnings.create', 'earnings.update', 'bonus.view', 'bonus.create', 'bonus.update',
            'penalty.view', 'penalty.create', 'penalty.update', 'deduction.view', 'deduction.create',
            'deduction.update', 'model.view.all', 'model.create', 'model.update', 'studio.view.own',
            'studio.view.profit', 'shift.view', 'shift.manage', 'metrics.view', 'reports.view',
            'user.create', 'user.update',
        ]);

        $monitor = Role::firstOrCreate([
            'name' => 'Monitor',
            'guard_name' => 'web'
        ]);

        $monitor->givePermissionTo([

            'activity.create',
            'activity.view',

            'earnings.view.model', 'earnings.view.studio', 'earnings.create', 'earnings.update',

            'model.view.all',

            'studio.view.own',

            'model.view.own', 'model.create', 'model.update', 'deduction.view', 'deduction.create',
            'deduction.update', 'shift.view', 'shift.manage', 'metrics.view', 'reports.view'
        ]);

        $model = Role::firstOrCreate([
            'name' => 'Performance',
            'guard_name' => 'web'
        ]);

        $model->givePermissionTo([

            'earnings.view.own',

            'model.view.own',

            'activity.create' 

        ]);
    }
}
