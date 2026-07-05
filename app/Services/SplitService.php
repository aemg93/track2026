<?php

namespace App\Services;

use App\Models\Performance;

class SplitService
{
    public function calculate(
        Performance $performance,
        float $grossUsd
    ): array {

        $split = $performance->split;

        $modelPercentage = $split?->model_percentage ?? 60;

        $studioPercentage = $split?->studio_percentage ?? 40;

        $modelShare = round(
            $grossUsd * ($modelPercentage / 100),
            2
        );

        $studioShare = round(
            $grossUsd * ($studioPercentage / 100),
            2
        );

        return [

            'gross_usd' => round($grossUsd, 2),

            'model_percentage' => $modelPercentage,

            'studio_percentage' => $studioPercentage,

            'model_share_usd' => $modelShare,

            'studio_share_usd' => $studioShare,

        ];
    }
}