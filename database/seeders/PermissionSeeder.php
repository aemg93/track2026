<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            'activity.create', 'activity.view',
            'earnings.view.own', 'earnings.view.model', 'earnings.view.studio',
            'earnings.create', 'earnings.update',
            'bonus.view', 'bonus.create', 'bonus.update',
            'penalty.view', 'penalty.create', 'penalty.update',
            'deduction.view', 'deduction.create', 'deduction.update',
            'model.view.own', 'model.view.all', 'model.create', 'model.update',
            'studio.view.own', 'studio.view.all', 'studio.view.profit',
            'shift.view', 'shift.manage', 'metrics.view', 'reports.view',
            'user.create', 'user.update', 'user.delete',
            'role.manage', 'permission.manage', 'split.update', 'dashboard.super',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}
