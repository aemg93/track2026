<?php

namespace App\Services;

use App\Models\Performance;

class RankingService
{
    public function recalculate(
        int $performanceId
    ): void {

        $performance = Performance::findOrFail(
            $performanceId
        );

        /*
        |--------------------------------------------------------------------------
        | AGREGADOS DESDE PERFORMANCE_PLATFORM
        |--------------------------------------------------------------------------
        */

        $hours = (float) $performance
            ->platforms()
            ->sum('performance_platform.hours_streamed');

        $earnings = (float) $performance
            ->platforms()
            ->sum('performance_platform.earnings_usd');

        /*
        |--------------------------------------------------------------------------
        | AJUSTES FINANCIEROS
        |--------------------------------------------------------------------------
        */

        $bonuses = (float) $performance
            ->bonuses()
            ->sum('amount');

        $penalties = (float) $performance
            ->penalties()
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | SCORE FINAL
        |--------------------------------------------------------------------------
        */

        $score = $this->calculateScore(
            $earnings,
            $bonuses,
            $penalties,
            $hours
        );

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZACIÓN DEL AGREGADO
        |--------------------------------------------------------------------------
        */

        $performance->update([

            'hours_streamed' => round($hours),

            'ranking_score' => $score,

        ]);
    }

    private function calculateScore(
        float $earnings,
        float $bonuses,
        float $penalties,
        float $hours
    ): float {

        return round(

            ($earnings * 0.60)

            +

            ($hours * 2)

            +

            $bonuses

            -

            $penalties,

            2
        );
    }
}