<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityService
{
    public function create(array $data): ActivityLog
    {
        // 1. Guardar actividad del monitor
        $activity = ActivityLog::create($data);

        // 2. Convertir automáticamente a ingreso real
        app(EarningsService::class)->create([
            'performance_id' => $data['performance_id'],
            'platform_id'    => $data['platform_id'],
            'user_id'        => $data['user_id'],
            'amount'         => $data['tokens'],
            'date'           => $data['date'],
        ]);

        return $activity;
    }
}