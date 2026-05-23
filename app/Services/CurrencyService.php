<?php

namespace App\Services;

class CurrencyService
{
    // temporal

    private float $copRate = 4200;

    public function usdToCop(
        float $usd
    ): float
    {
        return round(
            $usd * $this->copRate,
            2
        );
    }
}