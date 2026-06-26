<?php

namespace App\Services;

use App\Models\Performance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatisticsService
{

    public function performanceStats(
        Performance $performance
    ): array {

        return [

            'today' => $this->cached(
                $performance,
                'today',
                fn () => $this->sumRange(
                    $performance,
                    Carbon::today(),
                    Carbon::today()
                )
            ),


            'weekly' => $this->cached(
                $performance,
                'weekly',
                fn () => $this->sumRange(
                    $performance,
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                )
            ),


            'biweekly' => $this->cached(
                $performance,
                'biweekly',
                fn () => $this->sumRange(
                    $performance,
                    Carbon::now()->subDays(15),
                    Carbon::now()
                )
            ),


            'monthly' => $this->cached(
                $performance,
                'monthly',
                fn () => $this->sumRange(
                    $performance,
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                )
            ),

        ];
    }



    private function cached(
        Performance $performance,
        string $range,
        callable $callback
    ) {

        return Cache::remember(
            $this->cacheKey(
                $performance->id,
                $range
            ),
            now()->addMinutes(10),
            $callback
        );

    }



    private function cacheKey(
        int $performanceId,
        string $range
    ): string {

        return "stats:v3:performance:{$performanceId}:{$range}";

    }



    private function sumRange(
        Performance $performance,
        Carbon $start,
        Carbon $end
    ): array {


        $earnings = $performance
            ->earnings()
            ->where(function ($query) use ($start, $end) {

                $query
                    ->where(
                        'period_start',
                        '<=',
                        $end->toDateString()
                    )
                    ->where(
                        'period_end',
                        '>=',
                        $start->toDateString()
                    );

            })
            ->get();



        return [

            'gross_usd' => round(
                (float) $earnings->sum('gross_usd'),
                2
            ),


            'bonus_usd' => round(
                (float) $earnings->sum('bonus_usd'),
                2
            ),


            'penalty_usd' => round(
                (float) $earnings->sum('penalty_usd'),
                2
            ),


            'deduction_usd' => round(
                (float) $earnings->sum('deduction_usd'),
                2
            ),


            'net_usd' => round(
                (float) $earnings->sum('net_usd'),
                2
            ),


            'model_usd' => round(
                (float) $earnings->sum('model_share_usd'),
                2
            ),


            'studio_usd' => round(
                (float) $earnings->sum('studio_share_usd'),
                2
            ),

        ];

    }

}