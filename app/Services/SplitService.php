<?php

namespace App\Services;

use App\Models\Performance;
use InvalidArgumentException;

class SplitService
{
    private const DEFAULT_MODEL_PERCENTAGE = 60;
    private const DEFAULT_STUDIO_PERCENTAGE = 40;

    private const MIN_MODEL_PERCENTAGE = 50;
    private const MAX_STUDIO_PERCENTAGE = 50;

    public function calculate(
        Performance $performance,
        float $grossUsd
    ): array {

        $modelPercentage = $performance->split?->model_percentage
            ?? self::DEFAULT_MODEL_PERCENTAGE;

        $studioPercentage = $performance->split?->studio_percentage
            ?? self::DEFAULT_STUDIO_PERCENTAGE;

        $this->validate(
            $modelPercentage,
            $studioPercentage
        );

        return [

            'gross_usd' => round($grossUsd, 2),

            'model_percentage' => $modelPercentage,

            'studio_percentage' => $studioPercentage,

            'model_share_usd' => round(
                $grossUsd * ($modelPercentage / 100),
                2
            ),

            'studio_share_usd' => round(
                $grossUsd * ($studioPercentage / 100),
                2
            ),

        ];
    }

    public function validate(
        float $modelPercentage,
        float $studioPercentage
    ): void {

        if ($modelPercentage < self::MIN_MODEL_PERCENTAGE) {

            throw new InvalidArgumentException(
                'La modelo debe recibir mínimo el 50% de las ganancias.'
            );

        }

        if ($studioPercentage > self::MAX_STUDIO_PERCENTAGE) {

            throw new InvalidArgumentException(
                'El estudio no puede recibir más del 50% de las ganancias.'
            );

        }

        if (
            round(
                $modelPercentage + $studioPercentage,
                2
            ) !== 100.00
        ) {

            throw new InvalidArgumentException(
                'El porcentaje de la modelo y del estudio debe sumar exactamente 100%.'
            );

        }

    }

    public function defaults(): array
    {

        return [

            'model_percentage'  => self::DEFAULT_MODEL_PERCENTAGE,

            'studio_percentage' => self::DEFAULT_STUDIO_PERCENTAGE,

        ];

    }
}