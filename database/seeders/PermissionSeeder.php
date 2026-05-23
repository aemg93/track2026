<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Activity
            'activity.create',
            'activity.view',

            // Earnings
            'earnings.view.own',
            'earnings.view.model',
            'earnings.view.studio',

            // Finance
            'bonus.create',
            'penalty.create',

            // Models
            'model.view.own',
            'model.view.all',

            // Studio
            'studio.view.own',
            'studio.view.profit',

            // Dashboard
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