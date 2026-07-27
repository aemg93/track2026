<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MonitorSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'Monitor',
            'guard_name' => 'web',
        ]);


        $user = User::updateOrCreate(
            [
                'email' => 'monitor@example.com',
            ],
            [
                'name' => 'Monitor Test',
                'password' => bcrypt('password'),
            ]
        );


        $user->assignRole($role);
    }
}