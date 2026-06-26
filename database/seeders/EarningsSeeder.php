<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Performance;
use App\Models\Deduction;
use App\Models\Earning;
use App\Services\EarningService;

class EarningsSeeder extends Seeder
{
    public function run(): void
    {
        // Performance 1
        $performance1 = Performance::find(1);

        // Crear deducciones individuales
        Deduction::create([
            'performance_id' => $performance1->id,
            'user_id'        => 1,
            'category'       => 'Bebidas',
            'reason'         => 'Red Bull',
            'amount'         => 15,
            'date'           => '2026-06-18',
        ]);

        Deduction::create([
            'performance_id' => $performance1->id,
            'user_id'        => 1,
            'category'       => 'Snacks',
            'reason'         => 'Papas Margarita',
            'amount'         => 8,
            'date'           => '2026-06-18',
        ]);

        Deduction::create([
            'performance_id' => $performance1->id,
            'user_id'        => 1,
            'category'       => 'Transporte',
            'reason'         => 'Taxi al estudio',
            'amount'         => 177,
            'date'           => '2026-06-18',
        ]);

        // Crear earning base
        $earning1 = Earning::create([
            'performance_id'    => $performance1->id,
            'period_start'      => '2026-06-01',
            'period_end'        => '2026-06-30',
            'gross_usd'         => 2500,
            'bonus_usd'         => 100,
            'penalty_usd'       => 50,
            'deduction_usd'     => 0, // se recalcula
            'net_usd'           => 0, // se recalcula
            'model_percentage'  => 60,
            'studio_percentage' => 40,
            'status'            => 'paid',
            'paid_at'           => now(),
        ]);

        // Sincronizar totales con las relaciones
        app(EarningService::class)->syncEarningTotals($earning1);

        // Performance 2
        $performance2 = Performance::find(2);

        Deduction::create([
            'performance_id' => $performance2->id,
            'user_id'        => 1,
            'category'       => 'Equipos',
            'reason'         => 'Auriculares',
            'amount'         => 100,
            'date'           => '2026-06-18',
        ]);

        $earning2 = Earning::create([
            'performance_id'    => $performance2->id,
            'period_start'      => '2026-06-01',
            'period_end'        => '2026-06-30',
            'gross_usd'         => 1800,
            'bonus_usd'         => 50,
            'penalty_usd'       => 25,
            'deduction_usd'     => 0, // se recalcula
            'net_usd'           => 0, // se recalcula
            'model_percentage'  => 60,
            'studio_percentage' => 40,
            'status'            => 'pending',
            'paid_at'           => null,
        ]);

        app(EarningService::class)->syncEarningTotals($earning2);
    }
}
