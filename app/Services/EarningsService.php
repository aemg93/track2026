<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Earning;

class EarningsSeeder extends Seeder
{
    public function run(): void
    {
        Earning::create([
            'performance_id' => 1,
            'platform_id' => 1,
            'user_id' => 2,
            'amount' => 120,
            'amount_usd' => 120,
            'date' => now()->toDateString(),
        ]);

        Earning::create([
            'performance_id' => 1,
            'platform_id' => 2,
            'user_id' => 2,
            'amount' => 80,
            'amount_usd' => 80,
            'date' => now()->toDateString(),
        ]);

        Earning::create([
            'performance_id' => 2,
            'platform_id' => 3,
            'user_id' => 3,
            'amount' => 150,
            'amount_usd' => 150,
            'date' => now()->toDateString(),
        ]);
    }
}