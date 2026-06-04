<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // ==============================
        // RELACIONES SEGURAS
        // ==============================
        $earnings = $this->earnings ?? collect();
        $bonuses = $this->bonuses ?? collect();
        $penalties = $this->penalties ?? collect();

        // ==============================
        // CALCULOS FINANCIEROS
        // ==============================
        $totalEarnings = $earnings->sum(
            fn ($e) => $e->amount_usd ?? $e->amount ?? 0
        );

        $totalBonuses = $bonuses->sum('amount');
        $totalPenalties = $penalties->sum('amount');

        $net = ($totalEarnings + $totalBonuses) - $totalPenalties;

        return [

            /*
            |--------------------------------------------------------------------------
            | IDENTIDAD DEL MODELO
            |--------------------------------------------------------------------------
            */

            'id' => $this->id,

            'studio_id' => $this->studio_id,
            'user_id' => $this->user_id,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,

            /*
            |--------------------------------------------------------------------------
            | CONTACTO
            |--------------------------------------------------------------------------
            */

            'email' => $this->email,
            'phone' => $this->phone,

            'country' => $this->country,
            'city' => $this->city,
            'address' => $this->address,

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTACIÓN
            |--------------------------------------------------------------------------
            */

            'document_type' => $this->document_type,
            'document_number' => $this->document_number,

            'birth_date' => $this->birth_date,
            'profile_photo' => $this->profile_photo,

            /*
            |--------------------------------------------------------------------------
            | MÉTRICAS
            |--------------------------------------------------------------------------
            */

            'active' => (bool) $this->active,
            'status' => $this->active ? 'active' : 'inactive',

            'hours_streamed' => (int) $this->hours_streamed,
            'ranking_score' => (float) $this->ranking_score,

            /*
            |--------------------------------------------------------------------------
            | RELACIONES (RAW FRONT)
            |--------------------------------------------------------------------------
            */

            'earnings' => $earnings->map(fn ($e) => [
                'id' => $e->id,
                'amount' => (float) $e->amount,
                'amount_usd' => (float) ($e->amount_usd ?? $e->amount),
                'date' => $e->date,
            ])->values(),

            'bonuses' => $bonuses->map(fn ($b) => [
                'id' => $b->id,
                'amount' => (float) $b->amount,
                'reason' => $b->reason,
                'date' => $b->date,
            ])->values(),

            'penalties' => $penalties->map(fn ($p) => [
                'id' => $p->id,
                'amount' => (float) $p->amount,
                'reason' => $p->reason,
                'date' => $p->date,
            ])->values(),

            /*
            |--------------------------------------------------------------------------
            | FINANZAS (CORE DEL SISTEMA)
            |--------------------------------------------------------------------------
            */

            'financials' => [
                'earnings' => (float) $totalEarnings,
                'bonuses' => (float) $totalBonuses,
                'penalties' => (float) $totalPenalties,
                'net' => (float) $net,
            ],

            /*
            |--------------------------------------------------------------------------
            | RELACIONES
            |--------------------------------------------------------------------------
            */

            'studio' => $this->whenLoaded('studio'),
            'user' => $this->whenLoaded('user'),
            'split' => $this->whenLoaded('split'),
        ];
    }
}