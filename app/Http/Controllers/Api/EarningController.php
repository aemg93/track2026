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

        $query = Earning::with('performance');

        if ($user->hasRole('Performance')) {
            $query->where('user_id', $user->id);
        }

        if ($user->hasRole('Admin')) {
            $query->whereHas('performance', function ($q) use ($user) {
                $q->where('studio_id', $user->studio_id);
            });
        }

        $earnings = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $earnings
        ]);
    }
}