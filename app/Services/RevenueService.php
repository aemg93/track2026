<?php

namespace App\Services;

use App\Models\Platform;
use App\Models\Performance;

class RevenueService
{
    public function calculate(
        Performance $performance,
        Platform $platform,
        float $amount
    ): array {

        $conversion=
            app(
                PlatformConversionService::class
            )->convert(
                $platform,
                $amount
            );

        $split=
            app(
                SplitService::class
            )->calculate(
                $performance,
                $conversion['usd']
            );

        return [

            'platform'=>$platform->name,

            'gross_usd'=>
                $conversion['usd'],

            'model_percentage'=>
                $split['model_percentage'],

            'studio_percentage'=>
                $split['studio_percentage'],

            'model_usd'=>
                $split['model_usd'],

            'studio_usd'=>
                $split['studio_usd']

        ];
    }
}