<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Performance;
use App\Models\Shift;
use Carbon\Carbon;

class PerformanceWorkTimeService
{
    public function weeklySeconds(
        Performance $performance
    ): int {

        return (int) $performance
            ->shifts()
            ->whereBetween(
                'started_at',
                [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]
            )
            ->get()
            ->sum(
                fn (Shift $shift) =>
                    $shift->workedSeconds()
            );
    }


    public function weeklyHours(
        Performance $performance
    ): float {

        return $this->toHours(
            $this->weeklySeconds($performance)
        );
    }


    public function totalSeconds(
        Performance $performance
    ): int {

        return (int) $performance
            ->shifts()
            ->get()
            ->sum(
                fn (Shift $shift) =>
                    $shift->workedSeconds()
            );
    }


    public function totalHours(
        Performance $performance
    ): float {

        return $this->toHours(
            $this->totalSeconds($performance)
        );
    }


    public function periodHours(
        Performance $performance,
        Carbon $from,
        Carbon $to
    ): float {

        $seconds = $performance
            ->shifts()
            ->whereBetween(
                'started_at',
                [
                    $from,
                    $to,
                ]
            )
            ->get()
            ->sum(
                fn (Shift $shift) =>
                    $shift->workedSeconds()
            );


        return $this->toHours($seconds);
    }


    private function toHours(
        int|float|string $seconds
    ): float {

        return round(
            (float) $seconds / 3600,
            2
        );
    }
}