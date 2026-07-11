<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function start(Performance $performance): JsonResponse
    {
        $shift = $this->service->start($performance);

        return response()->json([
            'message' => 'Turno iniciado correctamente.',
            'data' => $shift,
        ]);
    }

    public function pause(Shift $shift): JsonResponse
    {
        $shift = $this->service->pause($shift);

        return response()->json([
            'message' => 'Turno pausado.',
            'data' => $shift,
        ]);
    }

    public function resume(Shift $shift): JsonResponse
    {
        $shift = $this->service->resume($shift);

        return response()->json([
            'message' => 'Turno reanudado.',
            'data' => $shift,
        ]);
    }

    public function finish(Shift $shift): JsonResponse
    {
        $shift = $this->service->finish($shift);

        return response()->json([
            'message' => 'Turno finalizado.',
            'data' => $shift,
        ]);
    }
}