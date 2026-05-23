<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\EarningsService;

class EarningsSeeder extends Seeder
{
    public function run(): void
    {
        app(
            EarningsService::class
        )->create([

            'performance_id'=>1,

            'platform_id'=>1,

            'user_id'=>2,

            'amount'=>120,

            'date'=>now()
                ->toDateString()

        ]);

        app(
            EarningsService::class
        )->create([

            'performance_id'=>1,

            'platform_id'=>2,

            'user_id'=>2,

            'amount'=>80,

            'date'=>now()
                ->toDateString()

        ]);

        app(
            EarningsService::class
        )->create([

            'performance_id'=>2,

            'platform_id'=>3,

            'user_id'=>3,

            'amount'=>150,

            'date'=>now()
                ->toDateString()

        ]);

    }
}