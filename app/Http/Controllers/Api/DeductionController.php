<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deduction;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Deduction::with('performance');

        if ($user->hasRole('Admin')) {
            $query->whereHas('performance', function ($q) use ($user) {
                $q->where('studio_id', $user->studio_id);
            });
        }

        return response()->json([
            'data' => $query->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'performance_id' => ['required', 'exists:performances,id'],
            'category'       => ['required', 'string'],
            'reason'         => ['required', 'string', 'max:255'],
            'amount'         => ['required', 'numeric', 'min:0'],
            'date'           => ['required', 'date'],
        ]);

        $user = $request->user();

        $data['user_id'] = $user->id;

        // 💡 lógica de cuotas automática
        if ($data['amount'] > 100000) {

            $installments = 3;

            $data['is_installment'] = true;
            $data['installments'] = $installments;
            $data['installment_value'] = round($data['amount'] / $installments, 2);

        } else {
            $data['is_installment'] = false;
        }

        $deduction = Deduction::create($data);

        return response()->json([
            'message' => 'Deduction created successfully',
            'data' => $deduction
        ], 201);
    }
}