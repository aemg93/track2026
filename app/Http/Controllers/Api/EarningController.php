<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Earning::query()
            ->with('performance');

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Admin')) {

            $query->whereHas('performance', function ($q) use ($user) {
                $q->where('studio_id', $user->studio_id);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $earnings = $query
            ->orderByDesc('period_end')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $earnings,
        ]);
    }
}