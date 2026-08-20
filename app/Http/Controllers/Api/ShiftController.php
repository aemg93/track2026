<?php

declare(strict_types=1);

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
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function __construct(
        private readonly ShiftService $shiftService
    ) {
    }

    /**
     * Lista los turnos activos visibles para el usuario.
     */
    public function active(
        Request $request
    ): JsonResponse {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if ($user->hasRole('Super Admin')) {
            $shifts = $this->shiftService->active();
        } elseif (
            $user->hasRole('Admin') ||
            $user->hasRole('Monitor')
        ) {
            $shifts = $this->shiftService->activeByStudioId(
                (int) $user->studio_id
            );
        } elseif ($user->hasRole('Performance')) {
            $performance = $user->performance;

            if (! $performance) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            $shift = $this->shiftService->activeByPerformance(
                $performance
            );

            $shifts = $shift
                ? collect([$shift])
                : collect();
        } else {
            abort(403);
        }

        return response()->json([
            'success' => true,
            'data' => $shifts,
        ]);
    }

    /**
     * Inicia un turno para una Performance.
     *
     * StartShiftRequest se encarga de autorizar
     * el acceso a la Performance.
     */
    public function start(
        StartShiftRequest $request,
        Performance $performance
    ): JsonResponse {
        $shift = $this->shiftService->start(
            $performance
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Turno iniciado correctamente.',
            'data' =>
                $this->loadRelations($shift),
        ], 201);
    }

    /**
     * Pausa un turno activo.
     *
     * PauseShiftRequest se encarga de autorizar
     * el acceso al turno.
     */
    public function pause(
        PauseShiftRequest $request,
        Shift $shift
    ): JsonResponse {
        $shift = $this->shiftService->pause(
            $shift
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Turno pausado correctamente.',
            'data' =>
                $this->loadRelations($shift),
        ]);
    }

    /**
     * Reanuda un turno pausado.
     *
     * ResumeShiftRequest se encarga de autorizar
     * el acceso al turno.
     */
    public function resume(
        ResumeShiftRequest $request,
        Shift $shift
    ): JsonResponse {
        $shift = $this->shiftService->resume(
            $shift
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Turno reanudado correctamente.',
            'data' =>
                $this->loadRelations($shift),
        ]);
    }

    /**
     * Finaliza un turno.
     *
     * FinishShiftRequest se encarga de autorizar
     * el acceso al turno.
     */
    public function finish(
        FinishShiftRequest $request,
        Shift $shift
    ): JsonResponse {
        $shift = $this->shiftService->finish(
            $shift
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Turno finalizado correctamente.',
            'data' =>
                $this->loadRelations($shift),
        ]);
    }

    /**
     * Relaciones necesarias para respuestas
     * del módulo Shift.
     */
    private function loadRelations(
        Shift $shift
    ): Shift {
        return $shift->load([
            'performance:id,studio_id,user_id,first_name,last_name,nickname',
            'performance.user:id,name,email',
            'performance.platforms:id,name,type,conversion_rate,multiplier',
            'studio:id,name',
        ]);
    }
}
