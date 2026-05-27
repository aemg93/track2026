<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardService $service)
    {
        $user = $request->user();

        $data = $service->getData($user);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}