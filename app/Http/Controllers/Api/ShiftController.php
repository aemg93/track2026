<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinishShiftRequest;
use App\Http\Requests\PauseShiftRequest;
use App\Http\Requests\ResumeShiftRequest;
use App\Http\Requests\StartShiftRequest;
use App\Models\Performance;
use App\Models\Shift;
use App\Services\ShiftService;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{
    public function __construct(
        protected ShiftService $service
    ) {
    }

    public function active(): JsonResponse
    {
        return response()->json([
            'data' => $this->service->active(),
        ]);
    }

    public function start(
        StartShiftRequest $request,
        Performance $performance
    ): JsonResponse {
        $shift = $this->service->start($performance);

        return response()->json([
            'message' => 'Turno iniciado correctamente.',
            'data' => $shift->load([
                'performance',
                'studio',
            ]),
        ], 201);
    }

    public function pause(
        PauseShiftRequest $request,
        Shift $shift
    ): JsonResponse {
        $shift = $this->service->pause($shift);

        return response()->json([
            'message' => 'Turno pausado correctamente.',
            'data' => $shift->load([
                'performance',
                'studio',
            ]),
        ]);
    }

    public function resume(
        ResumeShiftRequest $request,
        Shift $shift
    ): JsonResponse {
        $shift = $this->service->resume($shift);

        return response()->json([
            'message' => 'Turno reanudado correctamente.',
            'data' => $shift->load([
                'performance',
                'studio',
            ]),
        ]);
    }

    public function finish(
        FinishShiftRequest $request,
        Shift $shift
    ): JsonResponse {
        $shift = $this->service->finish($shift);

        return response()->json([
            'message' => 'Turno finalizado correctamente.',
            'data' => $shift->load([
                'performance',
                'studio',
            ]),
        ]);
    }
}