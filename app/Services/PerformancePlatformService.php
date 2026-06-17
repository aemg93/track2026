<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\Platform;
use Carbon\Carbon;

class PerformancePlatformService
{
    public function record(
        Performance $performance,
        Platform $platform,
        array $data
    )
    {
        $recordedAt = $data['recorded_at'] ?? Carbon::now();

        $tokens = (float) ($data['tokens'] ?? 0);
        $hours  = (float) ($data['hours_streamed'] ?? 0);

        $usd = $this->calculateUsd(
            $platform,
            $tokens
        );

        $payload = [

            'hours_streamed' => $hours,

            'tokens' => $tokens,

            'earnings_usd' => $usd,

            'multiplier' => (float) $platform->multiplier,

            'conversion_rate' => (float) (
                $platform->conversion_rate ?? 0
            ),

            'recorded_at' => $recordedAt,
        ];

        $existing = $performance
            ->platforms()
            ->where('platform_id', $platform->id)
            ->wherePivot('recorded_at', $recordedAt)
            ->first();

        if ($existing) {

            $performance
                ->platforms()
                ->updateExistingPivot(
                    $platform->id,
                    $payload
                );

            return $payload;
        }

        $performance
            ->platforms()
            ->attach(
                $platform->id,
                $payload
            );

        return $payload;
    }

    private function calculateUsd(
        Platform $platform,
        float $tokens
    ): float {

        if ($platform->type === 'usd') {
            return $tokens;
        }

        return round(
            $tokens * (float) ($platform->conversion_rate ?? 0),
            2
        );
    }
}