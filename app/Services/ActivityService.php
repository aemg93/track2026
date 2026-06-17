<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityService
{
    public function create(array $data): ActivityLog
    {
        return ActivityLog::create([
            'performance_id' => $data['performance_id'],
            'platform_id'    => $data['platform_id'],
            'user_id'        => $data['user_id'],
            'tokens'         => $data['tokens'],
            'date'           => $data['date'],
        ]);
    }
}