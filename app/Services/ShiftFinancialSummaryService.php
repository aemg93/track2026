<?php

namespace App\Services;

use App\Models\Shift;
use Illuminate\Support\Collection;

class ShiftFinancialSummaryService
{
    /**
     * Genera el resumen financiero consolidado de un turno.
     *
     * Fuente de verdad:
     * - earnings
     * - platform
     * - conversiones almacenadas en earnings
     */
    public function summary(Shift $shift): array
    {
        $earnings = $this->earnings($shift);


        return [
            'total_usd' => round(
                $earnings->sum('gross_usd'),
                2
            ),

            'total_tokens' => $earnings
                ->where('original_currency', 'tokens')
                ->sum('original_amount'),


            'platforms' => $this->groupByPlatform(
                $earnings
            ),
        ];
    }


    /**
     * Obtiene earnings relacionados al turno.
     */
    protected function earnings(Shift $shift): Collection
    {
        return $shift
            ->performance
            ->earnings()
            ->with([
                'platform:id,name,type',
            ])
            ->whereBetween(
                'earned_at',
                [
                    $shift->started_at,
                    $shift->ended_at ?? now(),
                ]
            )
            ->get();
    }


    /**
     * Agrupa ganancias por plataforma.
     */
    protected function groupByPlatform(Collection $earnings): array
    {
        return $earnings
            ->groupBy(
                fn ($earning) =>
                    $earning->platform->name
            )
            ->map(
                function ($items, $platform) {

                    $currency =
                        $items
                            ->pluck('original_currency')
                            ->unique()
                            ->first();


                    return [
                        'platform' => $platform,

                        'currency' => strtoupper(
                            $currency
                        ),

                        'amount' => round(
                            $items->sum(
                                'original_amount'
                            ),
                            2
                        ),

                        'usd' => round(
                            $items->sum(
                                'gross_usd'
                            ),
                            2
                        ),
                    ];
                }
            )
            ->values()
            ->toArray();
    }
}