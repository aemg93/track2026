<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitorShift;
use App\Services\MonitorShiftReportService;

class MonitorShiftReportController extends Controller
{
    public function show(MonitorShift $shift, MonitorShiftReportService $service)
    {
        return response()->json(
            $service->generate($shift)
        );
    }
}
