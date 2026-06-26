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


        $hours = (float) $performance
            ->platforms()
            ->sum(
                'performance_platform.hours_streamed'
            );


        $gross = (float) $performance
            ->earnings()
            ->sum('gross_usd');


        $net = (float) $performance
            ->earnings()
            ->sum('net_usd');


        $score = $this->calculateScore(
            $gross,
            $net,
            $hours
        );


        $performance->update([

            'hours_streamed' => round($hours),

            'ranking_score' => $score,

        ]);

    }



    private function calculateScore(
        float $gross,
        float $net,
        float $hours
    ): float {


        return round(

            ($gross * 0.50)

            +

            ($net * 0.30)

            +

            ($hours * 2),

            2

        );

    }

}