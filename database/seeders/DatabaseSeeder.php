<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            PlatformSeeder::class,
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