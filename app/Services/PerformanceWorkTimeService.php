<?php

namespace App\Services;

use App\Models\Performance;
use Carbon\Carbon;

class PerformanceWorkTimeService
{
    public function weeklyHours(
        Performance $performance
    ): float {

        $seconds = $performance
            ->shifts()
            ->whereBetween(
                'started_at',
                [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]
            )
            ->sum('worked_seconds');


        return round(
            $seconds / 3600,
            2
        );
    }


    public function totalHours(
        Performance $performance
    ): float {

        $seconds = $performance
            ->shifts()
            ->sum('worked_seconds');


        return round(
            $seconds / 3600,
            2
        );
    }
}