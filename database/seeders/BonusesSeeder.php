<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BonusesSeeder extends Seeder
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

        // Bono Camila
        DB::table('bonuses')->updateOrInsert(
            [
                'performance_id' => $camila,
                'date' => now()->toDateString(),
            ],
            [
                'amount' => 50,
                'reason' => 'Máximo rendimiento',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Bono Valentina
        DB::table('bonuses')->updateOrInsert(
            [
                'performance_id' => $valentina,
                'date' => now()->toDateString(),
            ],
            [
                'amount' => 30,
                'reason' => 'Meta semanal alcanzada',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}