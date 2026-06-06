<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear rol si no existe
        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        // 2. Crear usuario
        $user = User::firstOrCreate(
            [
                'email' => 'admin@example.com'
            ],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        // 3. Asignar rol
        $user->assignRole($role);
    }
}