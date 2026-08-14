<?php

namespace App\Services;

use App\Models\MonitorShift;

class MonitorShiftReportService
{
    public function __construct(
        private MonitorShiftActivityService $activityService,
    ) {}


    /**
     * Genera el reporte operativo del turno del monitor.
     *
     * La asignación de la modelo al turno se determina por:
     *
     *     performance.work_shift
     *
     * La hora del earning NO determina el turno de la modelo.
     */
    public function generate(
        MonitorShift $monitorShift
    ): array {

        $monitorShift->load([
            'monitor',
            'studio',
        ]);


        $start = $monitorShift->started_at;

        $end = $monitorShift->ended_at ?? now();


        $activity = $this->activityService->activity(
            $monitorShift
        );


        $models = $activity['models'] ?? [];

        $movements = $activity['timeline'] ?? [];

        $platforms = $activity['platforms'] ?? [];


        /*
        |--------------------------------------------------------------------------
        | Contar modelos reales
        |--------------------------------------------------------------------------
        |
        | models ahora está dividido en:
        |
        | morning
        | afternoon
        | night
        |
        | Por eso no podemos hacer simplemente count($models).
        |
        */

        $modelsCount =
            collect($models)
                ->flatten(1)
                ->count();


        return [

            /*
            |--------------------------------------------------------------------------
            | Monitor
            |--------------------------------------------------------------------------
            */

            'monitor' => [

                'id' =>
                    $monitorShift->monitor_id,

                'name' =>
                    $monitorShift
                        ->monitor
                        ?->name,

            ],


            /*
            |--------------------------------------------------------------------------
            | Studio
            |--------------------------------------------------------------------------
            */

            'studio' => [

                'id' =>
                    $monitorShift->studio_id,

                'name' =>
                    $monitorShift
                        ->studio
                        ?->name,

            ],


            /*
            |--------------------------------------------------------------------------
            | Turno del monitor
            |--------------------------------------------------------------------------
            */

            'period' => [

                'started_at' =>
                    $start,

                'ended_at' =>
                    $end,

            ],


            /*
            |--------------------------------------------------------------------------
            | Resumen
            |--------------------------------------------------------------------------
            */

            'summary' => [

                'total_usd' =>
                    (float) (
                        $activity['summary']['total_usd']
                        ?? 0
                    ),


                'total_tokens' =>
                    (float) (
                        $activity['summary']['total_tokens']
                        ?? 0
                    ),


                'platforms' =>
                    $platforms,


                'models_count' =>
                    $modelsCount,


                'movements_count' =>
                    count($movements),

            ],


            /*
            |--------------------------------------------------------------------------
            | Modelos por turno asignado
            |--------------------------------------------------------------------------
            |
            | morning
            | afternoon
            | night
            |
            */

            'models' => [

                'morning' =>
                    $models['morning'] ?? [],

                'afternoon' =>
                    $models['afternoon'] ?? [],

                'night' =>
                    $models['night'] ?? [],

            ],


            /*
            |--------------------------------------------------------------------------
            | Movimientos
            |--------------------------------------------------------------------------
            */

            'movements' =>
                $movements,

        ];
    }
}