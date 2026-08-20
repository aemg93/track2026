<?php

namespace App\Services;

use App\Enums\EarningStatus;
use App\Models\Performance;

class RankingService
{
    public function __construct(
        private PerformanceWorkTimeService $workTime
    ) {
    }

    public function recalculate(
        int $performanceId
    ): void {

        $performance = Performance::findOrFail(
            $performanceId
        );


        $hours = $this->workTime->totalHours($performance);


        $gross = (float) $performance
            ->earnings()
            ->whereIn('status', [
                EarningStatus::Approved->value,
                EarningStatus::Paid->value,
            ])
            ->sum('gross_usd');


        $net = (float) $performance
            ->earnings()
            ->whereIn('status', [
                EarningStatus::Approved->value,
                EarningStatus::Paid->value,
            ])
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
