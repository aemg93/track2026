<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    public function log(
        $model,
        string $action,
        ?array $old = null
    ): void
    {
        $user = auth()->user();

        AuditLog::create([

            'user_id' => $user?->id,

            'role' => $user
                ? $user
                    ->getRoleNames()
                    ->first()
                : 'system',

            'action' => $action,

            'auditable_type' =>
                class_basename($model),

            'auditable_id' =>
                $model->id,

            'ip' =>
                request()?->ip(),

            'old_values' =>
                $old,

            'new_values' =>
                $model->toArray()

        ]);
    }
}