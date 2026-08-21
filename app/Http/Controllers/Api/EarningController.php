<?php

namespace App\Http\Controllers\Api;

use App\Enums\EarningStatus;
use App\Http\Controllers\Controller;
use App\Services\EarningService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use App\Models\Earning;

class EarningController extends Controller
{
    public function index(
        Request $request,
        EarningService $service
    ) {
        abort_unless($request->user()->can('viewAny', Earning::class), 403);
        return response()->json([
            'success' => true,
            'data' => $service->list(
                $request->user()
            ),
        ]);
    }

    public function store(
        Request $request,
        EarningService $service
    ) {
        $data = $request->validate([
            'performance_id' => [
                'required',
                'exists:performances,id',
            ],

            'platform_id' => [
                'required',
                'exists:platforms,id',
            ],

            'earned_at' => [
                'required',
                'date',
            ],

            'original_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'original_currency' => [
                'required',
                'in:usd,tokens',
            ],

            'monitor_shift_id' => [
                'nullable',
                'exists:monitor_shifts,id',
            ],

            'status' => [
                'nullable',
                new Enum(EarningStatus::class),
            ],

            'paid_at' => [
                'nullable',
                'date',
            ],
        ]);

        $earning = $service->create(
            $data,
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Earning created successfully',
            'data' => $earning,
        ], 201);
    }
}
