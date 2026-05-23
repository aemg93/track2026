<?php

namespace App\Services;

use App\Models\Platform;

class PlatformConversionService
{
    public function convert(
        Platform $platform,
        float $amount
    ): array {

        if($platform->type==='usd')
        {
            return [

                'original'=>$amount,

                'usd'=>$amount

            ];
        }

        $realTokens=

            $amount *
            $platform->multiplier;

        $usd=

            $realTokens *
            $platform->conversion_rate;

        return [

            'original_tokens'=>$amount,

            'real_tokens'=>$realTokens,

            'usd'=>$usd

        ];
    }
}