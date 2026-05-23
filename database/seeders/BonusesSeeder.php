<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BonusesSeeder extends Seeder
{
    public function run(): void
    {
        $modelA = DB::table('performances')
            ->where('name', 'Model A')
            ->value('id');

        $modelB = DB::table('performances')
            ->where('name', 'Model B')
            ->value('id');

        $superAdmin = DB::table('users')
            ->where('email', 'admin@example.com')
            ->first();

        $userId = $superAdmin->id;

        // Bono Model A
        DB::table('bonuses')->updateOrInsert(
            [
                'performance_id' => $modelA,
                'date' => now()->toDateString(),
            ],
            [
                'amount' => 50,
                'reason' => 'Top performance',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Bono Model B
        DB::table('bonuses')->updateOrInsert(
            [
                'performance_id' => $modelB,
                'date' => now()->toDateString(),
            ],
            [
                'amount' => 30,
                'reason' => 'Weekly goal reached',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}