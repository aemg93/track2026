<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenaltiesSeeder extends Seeder
{
    public function run(): void
    {
        $camila = DB::table('performances')
            ->where('email', 'camila@example.com')
            ->value('id');

        $valentina = DB::table('performances')
            ->where('email', 'valentina@example.com')
            ->value('id');

        $superAdmin = DB::table('users')
            ->where('email', 'admin@example.com')
            ->first();

        $userId = $superAdmin->id;

        DB::table('penalties')->updateOrInsert(
            [
                'performance_id' => $camila,
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

        DB::table('penalties')->updateOrInsert(
            [
                'performance_id' => $valentina,
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