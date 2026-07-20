<?php

namespace App\Services;

use App\Models\Shift;
use Illuminate\Support\Collection;

class ShiftFinancialSummaryService
{
    private const UNKNOWN_PLATFORM = 'Sin plataforma';


    /**
     * Genera el resumen financiero consolidado de un turno.
     *
     * Fuente de verdad:
     * - earnings
     * - conversiones almacenadas en earnings
     * - plataforma asociada al earning
     *
     * Sólo incluye movimientos registrados
     * dentro del rango temporal del turno.
     */
    public function summary(Shift $shift): array
    {
        $earnings = $this->earnings($shift);


        return [
            'total_usd' => round(
                $earnings->sum('gross_usd'),
                2
            ),

            'total_tokens' => round(
                $earnings
                    ->where('original_currency', 'tokens')
                    ->sum('original_amount'),
                2
            ),

            'platforms' => $this->groupByPlatform(
                $earnings
            ),
        ];
    }


    /**
     * Obtiene earnings pertenecientes al turno.
     *
     * @return Collection<int,\App\Models\Earning>
     */
    protected function earnings(Shift $shift): Collection
    {
        if (! $shift->performance) {
            return collect();
        }


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
     * Agrupa earnings por plataforma.
     *
     * @param Collection<int,\App\Models\Earning> $earnings
     */
    protected function groupByPlatform(
        Collection $earnings
    ): array {

        return $earnings

            ->groupBy(
                fn ($earning) =>
                    $earning->platform?->name
                    ?? self::UNKNOWN_PLATFORM
            )

            ->map(
                function (
                    Collection $items,
                    string $platformName
                ) {


                    $first = $items->first();


                    return [

                        'platform_id' =>
                            $first?->platform?->id,

                        'platform_name' =>
                            $platformName,

                        'type' =>
                            $first?->platform?->type,


                        'currencies' =>
                            $items
                                ->pluck(
                                    'original_currency'
                                )
                                ->unique()
                                ->map(
                                    fn ($currency) =>
                                        strtoupper($currency)
                                )
                                ->values()
                                ->toArray(),


                        'original_amount' =>
                            round(
                                $items->sum(
                                    'original_amount'
                                ),
                                2
                            ),


                        'usd' =>
                            round(
                                $items->sum(
                                    'gross_usd'
                                ),
                                2
                            ),
                    ];
                }
            )

            ->sortByDesc(
                fn ($platform) =>
                    $platform['usd']
            )

            ->values()

            ->toArray();
    }
}