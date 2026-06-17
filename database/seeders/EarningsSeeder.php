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

            'period_start' => '2026-06-01',
            'period_end'   => '2026-06-30',

            'gross_usd' => 2500,

            'bonus_usd'     => 100,
            'penalty_usd'   => 50,
            'deduction_usd' => 200,

            'net_usd' => 2350,

            'model_percentage'  => 60,
            'studio_percentage' => 40,

            'model_share_usd'  => 1410,
            'studio_share_usd' => 940,

            'status' => 'paid',

            'paid_at' => now(),
        ]);

        Earning::create([
            'performance_id' => 2,

            'period_start' => '2026-06-01',
            'period_end'   => '2026-06-30',

            'gross_usd' => 1800,

            'bonus_usd'     => 50,
            'penalty_usd'   => 25,
            'deduction_usd' => 100,

            'net_usd' => 1725,

            'model_percentage'  => 60,
            'studio_percentage' => 40,

            'model_share_usd'  => 1035,
            'studio_share_usd' => 690,

            'status' => 'pending',

            'paid_at' => null,
        ]);
    }
}