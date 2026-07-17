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
        private readonly ShiftService $shiftService
    ) {
    }


    public function active(): JsonResponse
    {
        return response()->json([
            'data' => $this->shiftService->active(),
        ]);
    }


    public function start(
        StartShiftRequest $request,
        Performance $performance
    ): JsonResponse {

        $shift = $this->shiftService->start($performance);

        return response()->json([
            'message' => 'Turno iniciado correctamente.',
            'data' => $this->loadRelations($shift),
        ], 201);
    }


    public function pause(
        PauseShiftRequest $request,
        Shift $shift
    ): JsonResponse {

        $shift = $this->shiftService->pause($shift);

        return response()->json([
            'message' => 'Turno pausado correctamente.',
            'data' => $this->loadRelations($shift),
        ]);
    }


    public function resume(
        ResumeShiftRequest $request,
        Shift $shift
    ): JsonResponse {

        $shift = $this->shiftService->resume($shift);

        return response()->json([
            'message' => 'Turno reanudado correctamente.',
            'data' => $this->loadRelations($shift),
        ]);
    }


    public function finish(
        FinishShiftRequest $request,
        Shift $shift
    ): JsonResponse {

        $shift = $this->shiftService->finish($shift);

        return response()->json([
            'message' => 'Turno finalizado correctamente.',
            'data' => $this->loadRelations($shift),
        ]);
    }


    /**
     * Relaciones necesarias para respuestas del módulo Shift.
     */
    private function loadRelations(Shift $shift): Shift
    {
        return $shift->load([
            'performance.user',
            'performance.platforms',
            'studio',
        ]);
    }
}