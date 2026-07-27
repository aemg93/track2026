<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitorShift;
use App\Services\MonitorShiftService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MonitorShiftController extends Controller
{
    public function __construct(
        private readonly MonitorShiftService $service
    ) {}


    public function start(
        Request $request
    ): JsonResponse {

        $data = $request->validate([

            'studio_id' => [
                'required',
                'exists:studios,id',
            ],

        ]);


        $monitorShift = $this->service->start(

            $request->user(),

            $data['studio_id']

        );


        return response()->json([

            'message' =>
                'Turno del monitor iniciado.',

            'data' =>
                $monitorShift->load([
                    'monitor',
                    'studio',
                ]),

        ], 201);
    }



    public function finish(
        Request $request,
        MonitorShift $monitorShift
    ): JsonResponse {


        $user = $request->user();


        if (
            ! $user->hasRole('Super Admin')
            &&
            $monitorShift->monitor_id !== $user->id
        ) {

            abort(
                403,
                'No puedes finalizar este turno.'
            );

        }


        $monitorShift =
            $this->service->finish(
                $monitorShift
            );


        return response()->json([

            'message' =>
                'Turno del monitor finalizado.',

            'data' =>
                $monitorShift->load([
                    'monitor',
                    'studio',
                ]),

        ]);
    }




    public function active(
        Request $request
    ): JsonResponse {


        return response()->json([

            'data' =>
                $this->service
                    ->activeByMonitor(
                        $request->user()
                    ),

        ]);
    }
}