<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $studioId = DB::table('studios')
            ->where('name', 'Test Studio')
            ->value('id');

        DB::table('performances')->insert([
            [
                'studio_id' => $studioId,
                'user_id' => null,
                'first_name' => 'Camila',
                'last_name' => 'Moreno',
                'nickname' => 'CamilaHot',
                'email' => 'camila@example.com',
                'phone' => '3001234567',
                'country' => 'Colombia',
                'city' => 'Bogotá',
                'address' => 'Calle 100 #10-20',
                'document_type' => 'CC',
                'document_number' => '1000123456',
                'birth_date' => '1998-05-12',
                'profile_photo' => null,
                'active' => true,
                'hours_streamed' => 120,
                'ranking_score' => 85.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'studio_id' => $studioId,
                'user_id' => null,
                'first_name' => 'Valentina',
                'last_name' => 'Gomez',
                'nickname' => 'ValeFire',
                'email' => 'valentina@example.com',
                'phone' => '3019876543',
                'country' => 'Colombia',
                'city' => 'Medellin',
                'address' => 'Carrera 45 #20-15',
                'document_type' => 'CC',
                'document_number' => '1000987654',
                'birth_date' => '1997-11-25',
                'profile_photo' => null,
                'active' => true,
                'hours_streamed' => 95,
                'ranking_score' => 72.30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}