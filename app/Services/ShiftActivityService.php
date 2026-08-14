<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ShiftActivityService
{
    public function __construct(
        protected ShiftFinancialSummaryService $financialSummaryService,
    ) {}

    /**
     * Construye toda la actividad financiera y operativa
     * correspondiente exclusivamente al período del turno.
     */
    public function activity(Shift $shift): array
    {
        [$from, $to] = $this->range($shift);

        $data = [
            'earnings' => $this->earnings(
                $shift,
                $from,
                $to
            ),

            'bonuses' => $this->bonuses(
                $shift,
                $from,
                $to
            ),

            'penalties' => $this->penalties(
                $shift,
                $from,
                $to
            ),

            'deductions' => $this->deductions(
                $shift,
                $from,
                $to
            ),
        ];

        return [
            'timeline' =>
                $this->buildTimeline($data),

            'summary' =>
                $this->buildSummary($data),

            'financial_summary' =>
                $this->financialSummaryService
                    ->summary($shift),
        ];
    }

    /**
     * Retorna únicamente la línea de tiempo del turno.
     */
    public function timeline(Shift $shift): array
    {
        return $this->activity($shift)['timeline'];
    }

    /**
     * Retorna únicamente las métricas resumidas del turno.
     */
    public function summary(Shift $shift): array
    {
        return $this->activity($shift)['summary'];
    }

    /**
     * Determina exactamente el período financiero del turno.
     *
     * Turno activo:
     *   started_at -> ahora
     *
     * Turno finalizado:
     *   started_at -> ended_at
     */
    private function range(Shift $shift): array
    {
        $from = $shift->started_at;

        $to = $shift->ended_at ?? now();

        return [
            $from,
            $to,
        ];
    }

    /**
     * Construye la línea de tiempo unificada.
     */
    private function buildTimeline(array $data): array
    {
        return collect()

            /*
             * EARNINGS
             */
            ->merge(
                $data['earnings']->map(
                    fn ($earning) => [
                        'id' =>
                            $earning->id,

                        'type' =>
                            'earning',

                        'title' =>
                            'Ganancia',

                        'description' =>
                            $earning->platform?->name,

                        'performed_by' =>
                            $earning->user?->name,

                        /*
                         * Valor financiero en USD.
                         */
                        'amount' =>
                            (float) $earning->gross_usd,

                        /*
                         * Valor original registrado.
                         *
                         * Este NO debe utilizarse para
                         * los totales operativos de tokens.
                         */
                        'tokens' =>
                            $earning->original_currency === 'tokens'
                                ? (float) $earning->original_amount
                                : null,

                        /*
                         * Tokens normalizados del sistema.
                         */
                        'real_tokens' =>
                            $earning->real_tokens !== null
                                ? (float) $earning->real_tokens
                                : null,

                        'currency' =>
                            $earning->original_currency,

                        'date' =>
                            $earning->earned_at,
                    ]
                )
            )

            /*
             * BONUSES
             */
            ->merge(
                $data['bonuses']->map(
                    fn ($bonus) => [
                        'id' =>
                            $bonus->id,

                        'type' =>
                            'bonus',

                        'title' =>
                            'Bono',

                        'description' =>
                            $bonus->reason,

                        'performed_by' =>
                            $bonus->user?->name,

                        'amount' =>
                            (float) $bonus->amount,

                        'tokens' =>
                            null,

                        'real_tokens' =>
                            null,

                        'currency' =>
                            'usd',

                        'date' =>
                            $bonus->created_at,
                    ]
                )
            )

            /*
             * PENALTIES
             */
            ->merge(
                $data['penalties']->map(
                    fn ($penalty) => [
                        'id' =>
                            $penalty->id,

                        'type' =>
                            'penalty',

                        'title' =>
                            'Penalización',

                        'description' =>
                            $penalty->reason,

                        'performed_by' =>
                            $penalty->user?->name,

                        'amount' =>
                            (float) $penalty->amount,

                        'tokens' =>
                            null,

                        'real_tokens' =>
                            null,

                        'currency' =>
                            'usd',

                        'date' =>
                            $penalty->created_at,
                    ]
                )
            )

            /*
             * DEDUCTIONS
             */
            ->merge(
                $data['deductions']->map(
                    fn ($deduction) => [
                        'id' =>
                            $deduction->id,

                        'type' =>
                            'deduction',

                        'title' =>
                            'Descuento',

                        'description' =>
                            $deduction->reason,

                        'performed_by' =>
                            $deduction->user?->name,

                        'amount' =>
                            (float) $deduction->amount,

                        'tokens' =>
                            null,

                        'real_tokens' =>
                            null,

                        'currency' =>
                            'usd',

                        'date' =>
                            $deduction->created_at,
                    ]
                )
            )

            /*
             * Más reciente primero.
             */
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    /**
     * Construye las métricas del turno.
     */
    private function buildSummary(array $data): array
    {
        /*
         * Ingresos brutos en USD.
         */
        $earnings =
            $data['earnings']->sum(
                fn ($earning) =>
                    (float) ($earning->gross_usd ?? 0)
            );

        /*
         * Tokens reales normalizados.
         *
         * IMPORTANTE:
         *
         * NO utilizar original_amount.
         *
         * original_amount = valor original registrado
         * en la plataforma.
         *
         * real_tokens = tokens normalizados que utiliza
         * el sistema para los totales operativos.
         */
        $tokens =
            $data['earnings']->sum(
                fn ($earning) =>
                    (float) (
                        $earning->real_tokens ?? 0
                    )
            );

        /*
         * Bonificaciones.
         */
        $bonuses =
            $data['bonuses']->sum(
                fn ($bonus) =>
                    (float) ($bonus->amount ?? 0)
            );

        /*
         * Penalizaciones.
         */
        $penalties =
            $data['penalties']->sum(
                fn ($penalty) =>
                    (float) ($penalty->amount ?? 0)
            );

        /*
         * Descuentos.
         */
        $deductions =
            $data['deductions']->sum(
                fn ($deduction) =>
                    (float) ($deduction->amount ?? 0)
            );

        /*
         * Neto operativo.
         */
        $net =
            $earnings
            + $bonuses
            - $penalties
            - $deductions;

        return [
            'earnings' =>
                round(
                    $earnings,
                    2
                ),

            'tokens' =>
                round(
                    $tokens,
                    0
                ),

            'bonuses' =>
                round(
                    $bonuses,
                    2
                ),

            'penalties' =>
                round(
                    $penalties,
                    2
                ),

            'deductions' =>
                round(
                    $deductions,
                    2
                ),

            'net' =>
                round(
                    $net,
                    2
                ),
        ];
    }

    /**
     * Obtiene las ganancias registradas dentro
     * del intervalo exacto del turno.
     */
    private function earnings(
        Shift $shift,
        Carbon $from,
        Carbon $to,
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift
            ->performance
            ->earnings()
            ->with([
                'platform',
                'user:id,name',
            ])
            ->whereBetween(
                'earned_at',
                [
                    $from,
                    $to,
                ]
            )
            ->get();
    }

    /**
     * Obtiene los bonos registrados dentro
     * del intervalo exacto del turno.
     */
    private function bonuses(
        Shift $shift,
        Carbon $from,
        Carbon $to,
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift
            ->performance
            ->bonuses()
            ->with([
                'user:id,name',
            ])
            ->whereBetween(
                'created_at',
                [
                    $from,
                    $to,
                ]
            )
            ->get();
    }

    /**
     * Obtiene las penalizaciones registradas dentro
     * del intervalo exacto del turno.
     */
    private function penalties(
        Shift $shift,
        Carbon $from,
        Carbon $to,
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift
            ->performance
            ->penalties()
            ->with([
                'user:id,name',
            ])
            ->whereBetween(
                'created_at',
                [
                    $from,
                    $to,
                ]
            )
            ->get();
    }

    /**
     * Obtiene los descuentos registrados dentro
     * del intervalo exacto del turno.
     */
    private function deductions(
        Shift $shift,
        Carbon $from,
        Carbon $to,
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift
            ->performance
            ->deductions()
            ->with([
                'user:id,name',
            ])
            ->whereBetween(
                'created_at',
                [
                    $from,
                    $to,
                ]
            )
            ->get();
    }
}