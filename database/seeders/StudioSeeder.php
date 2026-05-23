<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('studios')->insert([
            'name' => 'Test Studio',
            'location' => 'Bogotá',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
