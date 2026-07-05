<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            'activity.create',
            'activity.view',

            'earnings.view.own',
            'earnings.view.model',
            'earnings.view.studio',

            'bonus.create',
            'penalty.create',

            'model.view.own',
            'model.view.all',

            'studio.view.own',
            'studio.view.profit',

            'dashboard.super',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}