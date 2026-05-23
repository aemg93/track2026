<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenaltiesSeeder extends Seeder
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

        // Multa para Model A
        DB::table('penalties')->updateOrInsert(
            [
                'performance_id' => $modelA,
                'date' => now()->toDateString(),
            ],
            [
                'amount' => 25,
                'reason' => 'Late login',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Multa para Model B
        DB::table('penalties')->updateOrInsert(
            [
                'performance_id' => $modelB,
                'date' => now()->toDateString(),
            ],
            [
                'amount' => 15,
                'reason' => 'Policy violation',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}