<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'nickname'   => $this->nickname,

            'hours_streamed' => (int) $this->hours_streamed,
            'ranking_score'  => (float) $this->ranking_score,
            'status' => $this->active ? 'active' : 'inactive',

            // 🧱 STRIPE CONTRACT ÚNICO
            'financials' => [
                'earnings' => $this->earnings->map(fn ($e) => [
                    'id' => $e->id,
                    'amount' => (float) $e->amount,
                    'amount_usd' => (float) $e->amount_usd,
                    'date' => $e->date,
                ])->values(),

                'bonuses' => $this->bonuses->map(fn ($b) => [
                    'id' => $b->id,
                    'amount' => (float) $b->amount,
                ])->values(),

                'penalties' => $this->penalties->map(fn ($p) => [
                    'id' => $p->id,
                    'amount' => (float) $p->amount,
                ])->values(),

                'totals' => [
                    'earnings' => (float) $this->earnings->sum('amount_usd'),
                    'bonuses' => (float) $this->bonuses->sum('amount'),
                    'penalties' => (float) $this->penalties->sum('amount'),
                    'deductions' => 0,
                ],
            ],
        ];
    }
}