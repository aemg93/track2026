<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\Platform;
use App\Services\PerformancePlatformService;

class PerformanceTrackingService
{
    public function track(
        int $performanceId,
        int $platformId,
        array $data
    ) {
        $performance = Performance::findOrFail($performanceId);
        $platform = Platform::findOrFail($platformId);

        $tokens = $this->sanitizeTokens($data['tokens'] ?? 0);
        $hours = $this->sanitizeHours($data['hours_streamed'] ?? 0);
        $ranking = $this->sanitizeRanking($data['ranking_score'] ?? 0);

        $service = app(PerformancePlatformService::class);

        $snapshot = $service->record(
            $performance,
            $platform,
            [
                'tokens' => $tokens,
                'hours_streamed' => $hours,
                'ranking_score' => $ranking,
                'recorded_at' => $data['recorded_at'] ?? now(),
            ]
        );

        return [
            'performance_id' => $performance->id,
            'platform_id' => $platform->id,
            'tokens' => $tokens,
            'hours_streamed' => $hours,
            'efficiency' => $this->calculateEfficiency($tokens, $hours),
            'usd_value' => $snapshot->earnings_usd ?? 0,
            'snapshot' => $snapshot,
        ];
    }

    private function sanitizeTokens(float $tokens): float
    {
        return max(0, $tokens);
    }

    private function sanitizeHours(float $hours): float
    {
        return max(0, $hours);
    }

    private function sanitizeRanking(float $ranking): float
    {
        return min(100, max(0, $ranking));
    }

    private function calculateEfficiency(float $tokens, float $hours): float
    {
        return $hours > 0 ? $tokens / $hours : 0;
    }
}