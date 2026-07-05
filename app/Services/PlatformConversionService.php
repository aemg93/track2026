<?php

namespace App\Services;

use App\Models\Platform;

class PlatformConversionService
{
    public function convert(
        Platform $platform,
        float $amount
    ): array {

        if ($platform->type === 'usd') {

            return [

                'original_amount'   => round($amount, 2),
                'original_currency' => 'usd',

                'real_tokens' => null,

                'conversion_rate' => 1,
                'multiplier'      => 1,

                'gross_usd' => round($amount, 2),

            ];
        }

        $realTokens = round(
            $amount * $platform->multiplier,
            2
        );

        $grossUsd = round(
            $realTokens * $platform->conversion_rate,
            2
        );

        return [

            'original_amount'   => round($amount, 2),
            'original_currency' => 'tokens',

            'real_tokens' => $realTokens,

            'conversion_rate' => $platform->conversion_rate,
            'multiplier'      => $platform->multiplier,

            'gross_usd' => $grossUsd,

        ];
    }
}