<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deduction;
use App\Models\Performance;
use App\Services\FinancialSyncDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    public function __construct(
        private FinancialSyncDispatcher $financialSyncDispatcher
    ) {
    }

    public function index(
        Request $request
    ): JsonResponse {
        $user = $request->user();
        abort_unless($user->can('viewAny', Deduction::class), 403);

        $query = Deduction::query()
            ->with([
                'performance:id,studio_id,user_id,first_name,last_name,nickname',
            ]);

        if ($user->hasRole('Super Admin')) {
            // Acceso global.
        } elseif (
            $user->hasRole('Admin') ||
            $user->hasRole('Monitor')
        ) {
            $query->whereHas(
                'performance',
                function ($q) use ($user): void {
                    $q->where(
                        'studio_id',
                        $user->studio_id
                    );
                }
            );
        } elseif ($user->hasRole('Performance')) {
            $query->whereHas(
                'performance',
                function ($q) use ($user): void {
                    $q->where(
                        'user_id',
                        $user->id
                    );
                }
            );
        } else {
            $query->whereRaw('1 = 0');
        }

        return response()->json([
            'success' => true,

            'data' => $query
                ->latest('date')
                ->get()
                ->map(
                    function (Deduction $deduction): array {
                        return [
                            'id' =>
                                $deduction->id,

                            'type' =>
                                'deduction',

                            'performance' => [
                                'id' =>
                                    $deduction
                                        ->performance
                                        ?->id,

                                'name' =>
                                    $deduction
                                        ->performance
                                        ?->nickname,
                            ],

                            'category' =>
                                $deduction->category,

                            'reason' =>
                                $deduction->reason,

                            'amount' =>
                                (float) $deduction->amount,

                            'date' =>
                                $deduction->date,

                            'is_installment' =>
                                (bool) $deduction->is_installment,

                            'installments' =>
                                $deduction->installments,

                            'installment_value' =>
                                $deduction->installment_value !== null
                                    ? (float) $deduction->installment_value
                                    : null,
                        ];
                    }
                ),
        ]);
    }

    public function store(
        Request $request
    ): JsonResponse {
        $user = $request->user();

        $data = $request->validate([
            'performance_id' => [
                'required',
                'exists:performances,id',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'date' => [
                'required',
                'date',
            ],
        ]);

        /*
         * Performance y Monitor no pueden registrar
         * deducciones.
         */
        abort_unless($user->can('create', Deduction::class), 403);

        $performance = Performance::findOrFail(
            $data['performance_id']
        );

        /*
         * El Admin solamente puede operar dentro
         * de su studio.
         */
        if (
            $user->hasRole('Admin') &&
            ! $user->canAccessStudio(
                $performance->studio_id
            )
        ) {
            abort(
                403,
                'Outside your studio scope'
            );
        }

        /*
         * El usuario que registra la deducción.
         */
        $data['user_id'] = $user->id;

        /*
         * Por ahora conservamos la lógica existente
         * de cuotas.
         */
        if ((float) $data['amount'] > 100000) {
            $installments = 3;

            $data['is_installment'] = true;

            $data['installments'] =
                $installments;

            $data['installment_value'] =
                round(
                    (float) $data['amount'] /
                    $installments,
                    2
                );
        } else {
            $data['is_installment'] = false;
            $data['installments'] = null;
            $data['installment_value'] = null;
        }

        $deduction = Deduction::create(
            $data
        );

        /*
         * La deducción modifica el resumen financiero
         * de la Performance.
         *
         * La sincronización se ejecuta mediante Job.
         */
        $this->financialSyncDispatcher
            ->dispatchPerformance(
                $performance
            );

        return response()->json([
            'success' => true,

            'message' =>
                'Deduction created successfully',

            'data' => [
                'id' =>
                    $deduction->id,

                'type' =>
                    'deduction',

                'performance_id' =>
                    $deduction->performance_id,

                'category' =>
                    $deduction->category,

                'amount' =>
                    (float) $deduction->amount,

                'date' =>
                    $deduction->date,

                'is_installment' =>
                    (bool) $deduction->is_installment,

                'installments' =>
                    $deduction->installments,

                'installment_value' =>
                    $deduction->installment_value !== null
                        ? (float) $deduction->installment_value
                        : null,
            ],
        ], 201);
    }
}
