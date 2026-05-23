<?php

namespace App\Services;

use App\Models\Performance;

class RankingService
{
    public function recalculate(int $performanceId): void
    {
        $performance = Performance::findOrFail($performanceId);

        $earnings = $performance->earnings()
            ->sum('amount_usd');

        $bonuses = $performance->bonuses()
            ->sum('amount');

        $penalties = $performance->penalties()
            ->sum('amount');

        $hours = $performance->hours_streamed;

        $score = $this->calculateScore(
            $earnings,
            $bonuses,
            $penalties,
            $hours
        );

        $performance->update([
            'ranking_score' => $score
        ]);
    }

    private function calculateScore(
        float $earnings,
        float $bonuses,
        float $penalties,
        int $hours
    ): float {
        return
            ($earnings * 0.6)
            + ($hours * 2)
            + $bonuses
            - $penalties;
    }
}