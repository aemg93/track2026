<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ShiftStatus;
use App\Models\Performance;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Collection;

class ShiftDashboardService
{
    public function __construct(
        private ShiftService $shiftService,
        private ShiftActivityService $shiftActivityService,
        private ShiftFinancialSummaryService $shiftFinancialSummaryService,
    ) {}

    /**
     * Dashboard operativo según el rol del usuario.
     */
    public function data(User $user): array
    {
        $shifts = $this->getVisibleActiveShifts($user);

        return [
            'total_active_shifts' =>
                $shifts->count(),

            'active_models' =>
                $shifts
                    ->filter(
                        fn (Shift $shift): bool =>
                            $shift->isActive()
                    )
                    ->pluck('performance_id')
                    ->filter()
                    ->unique()
                    ->count(),

            'paused_models' =>
                $shifts
                    ->filter(
                        fn (Shift $shift): bool =>
                            $shift->isPaused()
                    )
                    ->pluck('performance_id')
                    ->filter()
                    ->unique()
                    ->count(),

            'active_shifts' =>
                $shifts
                    ->filter(
                        fn (Shift $shift): bool =>
                            $shift->performance !== null
                    )
                    ->map(
                        fn (Shift $shift): array =>
                            $this->buildShift($shift)
                    )
                    ->values()
                    ->toArray(),
        ];
    }

    /**
     * Obtiene únicamente los turnos activos que el usuario
     * tiene permitido visualizar.
     */
    private function getVisibleActiveShifts(
        User $user
    ): Collection {
        /*
         * Super Admin:
         * puede visualizar todos los turnos activos.
         */
        if ($user->hasRole('Super Admin')) {
            return $this->shiftService->active();
        }

        /*
         * Performance:
         * solamente puede visualizar sus propios turnos.
         */
        if ($user->hasRole('Performance')) {
            return Shift::query()
                ->whereIn(
                    'status',
                    [
                        ShiftStatus::Active->value,
                        ShiftStatus::Paused->value,
                    ]
                )
                ->whereHas(
                    'performance',
                    function ($query) use ($user): void {
                        $query->where(
                            'user_id',
                            $user->id
                        );
                    }
                )
                ->with([
                    'performance:id,studio_id,user_id,first_name,last_name,nickname',
                    'performance.platforms:id,name,type',
                    'studio:id,name',
                ])
                ->orderByDesc('started_at')
                ->get();
        }

        /*
         * Admin / Monitor:
         * solamente pueden visualizar los turnos de su studio.
         */
        return $this->shiftService
            ->activeByStudioId(
                (int) $user->studio_id
            );
    }

    /**
     * Construye la representación completa de un turno.
     */
    private function buildShift(
        Shift $shift
    ): array {
        $workedSeconds = max(
            0,
            (int) $shift->workedSeconds()
        );

        /*
         * Actividad operativa:
         * - timeline
         * - summary
         */
        $activity =
            $this->shiftActivityService
                ->activity($shift);

        /*
         * Resumen financiero oficial:
         *
         * - gross
         * - bonuses
         * - penalties
         * - deductions
         * - net
         * - model share
         * - studio share
         * - total tokens
         * - plataformas
         *
         * La lógica financiera permanece en
         * ShiftFinancialSummaryService.
         */
        $financialSummary =
            $this->shiftFinancialSummaryService
                ->summary($shift);

        return [
            'id' =>
                $shift->id,

            'status' =>
                $shift->status?->value,

            'started_at' =>
                $shift->started_at,

            'last_resumed_at' =>
                $shift->last_resumed_at,

            'paused_at' =>
                $shift->paused_at,

            'ended_at' =>
                $shift->ended_at,

            'worked_seconds' =>
                $workedSeconds,

            'total_paused_seconds' =>
                max(
                    0,
                    (int) $shift->total_paused_seconds
                ),

            'duration' => [
                'minutes' =>
                    intdiv(
                        $workedSeconds,
                        60
                    ),

                'hours' =>
                    round(
                        $workedSeconds / 3600,
                        2
                    ),

                'formatted' =>
                    $this->formatDuration(
                        $workedSeconds
                    ),
            ],

            'performance' =>
                $this->buildPerformance(
                    $shift->performance
                ),

            'studio' =>
                $shift->studio
                    ? [
                        'id' =>
                            $shift->studio->id,

                        'name' =>
                            $shift->studio->name,
                    ]
                    : null,

            'actions' => [
                'can_pause' =>
                    $shift->isActive(),

                'can_resume' =>
                    $shift->isPaused(),

                'can_finish' =>
                    ! $shift->isFinished(),
            ],

            'activity' => [
                /*
                 * Línea de tiempo:
                 * earnings, bonuses, penalties
                 * y deductions.
                 */
                'timeline' =>
                    $activity['timeline'],

                /*
                 * Resumen operativo de actividad.
                 */
                'metrics' =>
                    $activity['summary'],

                /*
                 * Resumen financiero oficial.
                 *
                 * NO proviene de ShiftActivityService.
                 */
                'financial_summary' =>
                    $financialSummary,
            ],
        ];
    }

    /**
     * Construye la información de la Performance.
     */
    private function buildPerformance(
        ?Performance $performance
    ): ?array {
        if (! $performance) {
            return null;
        }

        return [
            'id' =>
                $performance->id,

            'name' =>
                trim(
                    "{$performance->first_name} {$performance->last_name}"
                ),

            'nickname' =>
                $performance->nickname,

            'platforms' =>
                $this->buildPlatforms(
                    $performance
                ),
        ];
    }

    /**
     * Plataformas asociadas a la Performance.
     */
    private function buildPlatforms(
        Performance $performance
    ): array {
        return $performance
            ->platforms
            ->map(
                fn ($platform): array => [
                    'id' =>
                        $platform->id,

                    'name' =>
                        $platform->name,

                    'type' =>
                        $platform->type,
                ]
            )
            ->values()
            ->toArray();
    }

    /**
     * Formato de duración sin límite artificial de 24 horas.
     */
    private function formatDuration(
        int $seconds
    ): string {
        $hours = intdiv(
            $seconds,
            3600
        );

        $minutes = intdiv(
            $seconds % 3600,
            60
        );

        $remainingSeconds =
            $seconds % 60;

        return sprintf(
            '%02d:%02d:%02d',
            $hours,
            $minutes,
            $remainingSeconds
        );
    }

    /**
     * Performances que actualmente tienen
     * un turno activo o pausado.
     *
     * Este método devuelve información global.
     * Para endpoints restringidos por studio/usuario,
     * debe aplicarse el scope correspondiente en el controlador
     * o utilizar data(User $user).
     */
    public function activePerformances(): array
    {
        return Shift::query()
            ->whereIn(
                'status',
                [
                    ShiftStatus::Active->value,
                    ShiftStatus::Paused->value,
                ]
            )
            ->whereHas('performance')
            ->with([
                'performance:id,studio_id,user_id,first_name,last_name,nickname',
            ])
            ->get()
            ->filter(
                fn (Shift $shift): bool =>
                    $shift->performance !== null
            )
            ->map(
                fn (Shift $shift): array => [
                    'id' =>
                        $shift->performance->id,

                    'name' =>
                        trim(
                            "{$shift->performance->first_name} {$shift->performance->last_name}"
                        ),

                    'nickname' =>
                        $shift->performance->nickname,

                    'status' =>
                        $shift->status?->value,
                ]
            )
            ->values()
            ->toArray();
    }
}