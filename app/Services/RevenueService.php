<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\Platform;

class RevenueService
{
    public function __construct(
        private PlatformConversionService $conversionService,
        private SplitService $splitService
    ) {
    }

    public function calculate(
        Performance $performance,
        Platform $platform,
        float $amount
    ): array {

        $conversion = $this->conversionService->convert(
            $platform,
            $amount
        );

        $split = $this->splitService->calculate(
            $performance,
            $conversion['gross_usd']
        );

        return [

            'platform_id' => $platform->id,

            'original_amount' => $conversion['original_amount'],

            'original_currency' => $conversion['original_currency'],

            'real_tokens' => $conversion['real_tokens'],

            'conversion_rate' => $conversion['conversion_rate'],

            'multiplier' => $conversion['multiplier'],

            'gross_usd' => $conversion['gross_usd'],

            'net_usd' => $conversion['gross_usd'],

            'model_percentage' => $split['model_percentage'],

            'studio_percentage' => $split['studio_percentage'],

            'model_share_usd' => $split['model_share_usd'],

            'studio_share_usd' => $split['studio_share_usd'],

        ];
    }
}