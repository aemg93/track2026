<?php

namespace Database\Seeders;

use App\Models\Deduction;
use App\Models\Earning;
use App\Models\Performance;
use App\Services\EarningService;
use Illuminate\Database\Seeder;

class EarningsSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Performance 1
        |--------------------------------------------------------------------------
        */

        $performance1 = Performance::findOrFail(1);

        $this->createDeduction(
            $performance1,
            'Bebidas',
            'Red Bull',
            15
        );

        $this->createDeduction(
            $performance1,
            'Snacks',
            'Papas Margarita',
            8
        );

        $this->createDeduction(
            $performance1,
            'Transporte',
            'Taxi al estudio',
            177
        );

        $this->createEarning(
            $performance1,
            2500,
            'paid',
            now()
        );

        /*
        |--------------------------------------------------------------------------
        | Performance 2
        |--------------------------------------------------------------------------
        */

        $performance2 = Performance::findOrFail(2);

        $this->createDeduction(
            $performance2,
            'Equipos',
            'Auriculares',
            100
        );

        $this->createEarning(
            $performance2,
            1800,
            'pending'
        );
    }

    private function createDeduction(
        Performance $performance,
        string $category,
        string $reason,
        float $amount
    ): void {
        Deduction::create([
            'performance_id' => $performance->id,
            'user_id'        => 1,
            'category'       => $category,
            'reason'         => $reason,
            'amount'         => $amount,
            'date'           => '2026-06-18',
        ]);
    }

    private function createEarning(
        Performance $performance,
        float $grossUsd,
        string $status,
        $paidAt = null
    ): void {
        $earning = Earning::create([
            'performance_id'    => $performance->id,

            'period_start'      => '2026-06-01',
            'period_end'        => '2026-06-30',

            'gross_usd'         => $grossUsd,

            'bonus_usd'         => 0,
            'penalty_usd'       => 0,
            'deduction_usd'     => 0,
            'net_usd'           => 0,

            'model_percentage'  => 60,
            'studio_percentage' => 40,

            'model_share_usd'   => 0,
            'studio_share_usd'  => 0,

            'status'            => $status,
            'paid_at'           => $paidAt,
        ]);

        app(EarningService::class)->syncEarning($earning);
    }
}