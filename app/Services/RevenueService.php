<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\Platform;

class RevenueService
{
    public function __construct(
        private PlatformConversionService $conversionService,
        private SplitService $splitService
    ) {}

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
            $conversion['usd']
        );

        return [

            'platform' =>
                $platform->name,

            'gross_usd' =>
                $conversion['usd'],

            'model_percentage' =>
                $split['model_percentage'],

            'studio_percentage' =>
                $split['studio_percentage'],

            'model_usd' =>
                $split['model_usd'],

            'studio_usd' =>
                $split['studio_usd'],

        ];
    }
}