<?php

namespace Database\Seeders;

use App\Models\Deduction;
use App\Models\Performance;
use App\Models\Platform;
use App\Services\EarningService;
use Illuminate\Database\Seeder;

class EarningsSeeder extends Seeder
{
    public function run(): void
    {
        $performance1 = Performance::findOrFail(1);

        $platformToken = Platform::where('type', 'token')
            ->firstOrFail();

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
            $platformToken,
            2500,
            'tokens',
            'paid',
            now()
        );

        /*
        |--------------------------------------------------------------------------
        | Performance 2
        |--------------------------------------------------------------------------
        */

        $performance2 = Performance::findOrFail(2);

        $platformUsd = Platform::where('type', 'usd')
            ->firstOrFail();

        $this->createDeduction(
            $performance2,
            'Equipos',
            'Auriculares',
            100
        );

        $this->createEarning(
            $performance2,
            $platformUsd,
            1800,
            'usd',
            'draft'
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

            'user_id' => 1,

            'category' => $category,

            'reason' => $reason,

            'amount' => $amount,

            'date' => '2026-06-18',

        ]);

    }

    private function createEarning(
        Performance $performance,
        Platform $platform,
        float $amount,
        string $currency,
        string $status,
        $paidAt = null
    ): void {

        app(EarningService::class)->create([

            'performance_id' => $performance->id,

            'platform_id' => $platform->id,

            'earned_at' => now(),

            'original_amount' => $amount,

            'original_currency' => $currency,

            'status' => $status,

            'paid_at' => $paidAt,

        ]);

    }
}