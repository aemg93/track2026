<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        // Obtenemos el ID del estudio de prueba
        $studioId = DB::table('studios')->where('name', 'Test Studio')->value('id');

        DB::table('performances')->insert([
            [
                'studio_id' => $studioId,
                'name' => 'Model A',
                'active' => true,
                'hours_streamed' => 12,
                'ranking_score' => 85.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'studio_id' => $studioId,
                'name' => 'Model B',
                'active' => true,
                'hours_streamed' => 8,
                'ranking_score' => 72.3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
