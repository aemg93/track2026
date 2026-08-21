<?php

namespace App\Services;

use App\Enums\EarningStatus;
use App\Enums\MonitorShiftStatus;
use App\Models\Earning;
use App\Models\MonitorShift;
use App\Models\Performance;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EarningService
{
    public function __construct(
        private RevenueService $revenueService,
        private AuditService $auditService
    ) {
    }

    public function list(User $user): LengthAwarePaginator
    {
        $query = Earning::query()
            ->with([
                'performance:id,studio_id,user_id,first_name,last_name,nickname',
                'platform:id,name,slug',
                'user:id,name,email',
                'monitorShift.monitor:id,name,email',
            ]);

        if ($user->hasRole('Super Admin')) {
            return $this->paginate($query);
        }

        if (
            $user->hasRole('Admin') ||
            $user->hasRole('Monitor')
        ) {
            $query->whereHas(
                'performance',
                function ($q) use ($user) {
                    $q->where(
                        'studio_id',
                        $user->studio_id
                    );
                }
            );
        }

        if ($user->hasRole('Performance')) {
            $query->whereHas(
                'performance',
                function ($q) use ($user) {
                    $q->where(
                        'user_id',
                        $user->id
                    );
                }
            );
        }

        return $this->paginate($query);
    }

    public function create(
        array $data,
        ?User $user = null
    ): Earning {
        /** @var User|null $user */
        $user ??= Auth::user();

        if (! $user) {
            abort(401);
        }

        /*
         * Una Performance nunca puede registrar
         * directamente sus propios earnings.
         */
        if ($user->hasRole('Performance')) {
            abort(
                403,
                'Not allowed to create earnings'
            );
        }

        $performance = Performance::findOrFail(
            $data['performance_id']
        );
        abort_unless($user->can('create', [Earning::class, $performance]), 403);

        /*
         * Admin y Monitor solamente pueden operar
         * dentro de su studio.
         */
        if (
            (
                $user->hasRole('Admin') ||
                $user->hasRole('Monitor')
            )
            &&
            ! $user->canAccessStudio(
                $performance->studio_id
            )
        ) {
            abort(
                403,
                'Outside your studio scope'
            );
        }

        $platform = Platform::findOrFail(
            $data['platform_id']
        );

        /*
         * Si no llega turno explícito, buscamos
         * el turno activo del mismo studio.
         */
        $monitorShiftId =
            $data['monitor_shift_id'] ?? null;

        if ($monitorShiftId !== null) {
            $monitorShift = MonitorShift::findOrFail(
                $monitorShiftId
            );

            /*
             * El turno debe pertenecer al mismo studio
             * de la Performance.
             */
            if (
                (int) $monitorShift->studio_id !==
                (int) $performance->studio_id
            ) {
                abort(
                    403,
                    'Monitor shift outside performance studio'
                );
            }
        } else {
            $monitorShift = MonitorShift::query()
                ->where(
                    'studio_id',
                    $performance->studio_id
                )
                ->where(
                    'status',
                    MonitorShiftStatus::Active
                )
                ->latest('started_at')
                ->first();

            $monitorShiftId =
                $monitorShift?->id;
        }

        /*
         * RevenueService es la única fuente de verdad
         * para convertir el ingreso de la plataforma.
         */
        $revenue = $this->revenueService->calculate(
            $performance,
            $platform,
            (float) $data['original_amount']
        );

        $earning = Earning::create([
            'performance_id' =>
                $performance->id,

            'platform_id' =>
                $platform->id,

            'monitor_shift_id' =>
                $monitorShiftId,

            'user_id' =>
                $user->id,

            'earned_at' =>
                $data['earned_at'] ?? now(),

            'original_amount' =>
                $revenue['original_amount'],

            'original_currency' =>
                $revenue['original_currency'],

            'real_tokens' =>
                $revenue['real_tokens'] ?? null,

            'conversion_rate' =>
                $revenue['conversion_rate'] ?? null,

            'multiplier' =>
                $revenue['multiplier'] ?? null,

            'gross_usd' =>
                $revenue['gross_usd'],

            /*
             * Los ajustes pertenecen al nivel Performance.
             * No se duplican dentro de cada earning.
             */
            'bonus_usd' =>
                0,

            'penalty_usd' =>
                0,

            'deduction_usd' =>
                0,

            'net_usd' =>
                round(
                    (float) $revenue['gross_usd'],
                    2
                ),

            'model_percentage' =>
                $revenue['model_percentage'],

            'studio_percentage' =>
                $revenue['studio_percentage'],

            'model_share_usd' =>
                round(
                    (float) $revenue['gross_usd'] *
                    (
                        (float) $revenue['model_percentage']
                        / 100
                    ),
                    2
                ),

            'studio_share_usd' =>
                round(
                    (float) $revenue['gross_usd'] *
                    (
                        (float) $revenue['studio_percentage']
                        / 100
                    ),
                    2
                ),

            'status' =>
                $data['status'] ?? 'draft',

            'paid_at' =>
                $data['paid_at'] ?? null,
        ]);

        $earning = $this->syncEarning($earning);
        $this->auditService->log($earning, 'created');
        return $earning;
    }

    /**
     * Sincroniza únicamente los valores propios
     * del earning.
     *
     * Bonos, penalizaciones y deducciones pertenecen
     * al resumen financiero de la Performance.
     */
    public function syncEarning(
        Earning $earning
    ): Earning {
        if (in_array($earning->status, [
            EarningStatus::Paid,
            EarningStatus::Cancelled,
        ], true)) {
            return $earning;
        }

        $earning->loadMissing([
            'performance',
            'platform',
            'user',
        ]);

        $earning->bonus_usd = 0;

        $earning->penalty_usd = 0;

        $earning->deduction_usd = 0;

        $earning->net_usd = round(
            (float) $earning->gross_usd,
            2
        );

        $earning->model_share_usd = round(
            $earning->net_usd *
            (
                (float) $earning->model_percentage
                / 100
            ),
            2
        );

        $earning->studio_share_usd = round(
            $earning->net_usd *
            (
                (float) $earning->studio_percentage
                / 100
            ),
            2
        );

        $earning->save();

        return $earning;
    }

    private function paginate(
        Builder $query
    ): LengthAwarePaginator {
        return $query
            ->orderByDesc('earned_at')
            ->paginate(25);
    }
}
