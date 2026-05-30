<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        DashboardService $service,
        FinancialService $financial
    )
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'dashboard' => $service->getData($user),

                'finance' => $financial->getSummary($user),
            ]
        ]);
    }
}