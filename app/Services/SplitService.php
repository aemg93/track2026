<?php

namespace App\Services;

use App\Models\Performance;

class SplitService
{
    public function calculate(Performance $performance, float $usdAmount): array
    {
        $split = $performance->split;

        if (!$split) {

            $modelPercent = 60;
            $studioPercent = 40;

        } else {

            $modelPercent = $split->model_percentage;
            $studioPercent = $split->studio_percentage;

        }

        $modelAmount = ($usdAmount * $modelPercent) / 100;

        $studioAmount = ($usdAmount * $studioPercent) / 100;

        return [

            'gross_usd' => $usdAmount,

            'model_percentage' => $modelPercent,

            'studio_percentage' => $studioPercent,

            'model_usd' => round($modelAmount,2),

            'studio_usd' => round($studioAmount,2)

        ];
    }
}