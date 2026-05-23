<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Performance;
use App\Models\PerformanceSplit;

class PerformanceSplitSeeder extends Seeder
{
    public function run(): void
    {
        Performance::all()->each(function ($performance) {

            PerformanceSplit::firstOrCreate(

                [
                    'performance_id'=>$performance->id
                ],

                [
                    'model_percentage'=>60,
                    'studio_percentage'=>40
                ]

            );

        });
    }
}