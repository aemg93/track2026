<?php

namespace App\Services;

use App\Enums\EarningStatus;
use App\Models\Performance;
use Illuminate\Support\Collection;

class PerformanceAnalyticsService
{
    public function summary(
        Performance $performance,
        $from = null,
        $to = null
    ) {
        $query = $performance->platforms();

        if ($from && $to) {
            $query->wherePivotBetween('recorded_at', [$from, $to]);
        }

        $platforms = $query->get();

        $earnings = $performance->earnings()
            ->whereIn('status', [
                EarningStatus::Approved->value,
                EarningStatus::Paid->value,
            ])
            ->when(
                $from && $to,
                fn ($q) => $q->whereBetween('earned_at', [$from, $to])
            )
            ->get();

        $totals = $this->calculateTotals($platforms, $earnings);

        return [
            'performance_id' => $performance->id,

            'totals' => $totals,

            'global_efficiency' => $this->calculateEfficiency($totals),

            'ranking_score' => $this->calculateRankingScore($totals),

            'platforms' => $this->buildPlatformBreakdown($platforms, $earnings),
        ];
    }

    private function buildPlatformBreakdown(Collection $platforms, Collection $earnings): array
    {
        return $platforms->map(function ($platform) use ($earnings) {

            $tokens = (float) ($platform->pivot->tokens ?? 0);
            $hours  = (float) ($platform->pivot->hours_streamed ?? 0);
            $usd = (float) $earnings
                ->where('platform_id', $platform->id)
                ->sum('gross_usd');

            $tokensPerHour = $hours > 0 ? $tokens / $hours : 0;
            $usdPerHour    = $hours > 0 ? $usd / $hours : 0;

            return [
                'platform_id' => $platform->id,
                'platform' => $platform->name,

                'metrics' => [
                    'tokens' => $tokens,
                    'hours'  => $hours,
                    'usd'    => $usd,
                ],

                'efficiency' => [
                    'tokens_per_hour' => round($tokensPerHour, 4),
                    'usd_per_hour'    => round($usdPerHour, 4),
                ],

                'score' => round($tokensPerHour + $usdPerHour, 2),
            ];
        })->sortByDesc('score')->values()->toArray();
    }

    private function calculateTotals(Collection $platforms, Collection $earnings): array
    {
        return [
            'tokens' => (float) $platforms->sum(fn ($p) => $p->pivot->tokens ?? 0),
            'hours'  => (float) $platforms->sum(fn ($p) => $p->pivot->hours_streamed ?? 0),
            'usd'    => (float) $earnings->sum('gross_usd'),
        ];
    }

    private function calculateEfficiency(array $totals): array
    {
        $hours = $totals['hours'];

        return [
            'tokens_per_hour' => $hours > 0 ? round($totals['tokens'] / $hours, 4) : 0,
            'usd_per_hour'    => $hours > 0 ? round($totals['usd'] / $hours, 4) : 0,
        ];
    }

    private function calculateRankingScore(array $totals): float
    {
        $hours = $totals['hours'];

        if ($hours <= 0) {
            return 0;
        }

        // fórmula base pro (ajustable luego con ML o weights)
        $tokenScore = $totals['tokens'] / $hours;
        $usdScore   = $totals['usd'] / $hours;

        return round(($tokenScore * 0.6) + ($usdScore * 0.4), 2);
    }
}
