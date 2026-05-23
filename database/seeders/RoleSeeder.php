<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // SUPER ADMIN
        // =========================
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);

        $superAdmin->givePermissionTo(Permission::all());

        // =========================
        // MONITOR (OPERADOR)
        // =========================
        $monitor = Role::firstOrCreate([
            'name' => 'Monitor',
            'guard_name' => 'web'
        ]);

        $monitor->givePermissionTo([

            'activity.create',
            'activity.view',

            'earnings.view.model',

            'model.view.all',

            'studio.view.own',

            'model.view.own'
        ]);

        // =========================
        // PERFORMANCE (MODELO)
        // =========================
        $model = Role::firstOrCreate([
            'name' => 'Performance',
            'guard_name' => 'web'
        ]);

        $model->givePermissionTo([

            'earnings.view.own',

            'model.view.own',

            'activity.create' // opcional si el modelo registra actividad

        ]);
    }
}