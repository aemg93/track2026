<?php

namespace App\Services;

use App\Models\MonitorShift;

class MonitorShiftReportService
{
    public function __construct(
        private MonitorShiftActivityService $activityService,
    ) {}


    /**
     * Genera reporte operativo del turno del monitor.
     *
     * Incluye:
     * - periodo supervisado
     * - producción total
     * - modelos
     * - plataformas
     * - movimientos
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


        return [

            'monitor' => [

                'id' =>
                    $monitorShift->monitor_id,

                'name' =>
                    $monitorShift->monitor?->name,

            ],


            'studio' => [

                'id' =>
                    $monitorShift->studio_id,

                'name' =>
                    $monitorShift->studio?->name,

            ],


            'period' => [

                'started_at' =>
                    $start,

                'ended_at' =>
                    $end,

            ],


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
                    $activity['platforms'] ?? [],


                'models_count' =>
                    count($models),


                'movements_count' =>
                    count($movements),

            ],


            'models' =>
                $models,


            'movements' =>
                $movements,

        ];
    }
}