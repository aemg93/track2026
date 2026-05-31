<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([

            PlatformSeeder::class,

            PermissionSeeder::class,

            RoleSeeder::class,

            SuperAdminSeeder::class,

            StudioSeeder::class,

            PerformanceSeeder::class,

            PerformanceSplitSeeder::class,

            PerformanceUserSeeder::class,

            EarningsSeeder::class,

            PenaltiesSeeder::class,

            BonusesSeeder::class,

        ]);
    }
}