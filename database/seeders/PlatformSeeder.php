<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Platform;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        Platform::updateOrCreate(

            ['name'=>'Cam4'],

            [

                'type'=>'token',

                'multiplier'=>2,

                'conversion_rate'=>0.05

            ]

        );

        Platform::updateOrCreate(

            ['name'=>'Stripchat'],

            [

                'type'=>'token',

                'multiplier'=>1,

                'conversion_rate'=>0.05

            ]

        );

        Platform::updateOrCreate(

            ['name'=>'Chaturbate'],

            [

                'type'=>'token',

                'multiplier'=>1,

                'conversion_rate'=>0.05

            ]

        );

        Platform::updateOrCreate(

            ['name'=>'Cams'],

            [

                'type'=>'usd',

                'multiplier'=>1,

                'conversion_rate'=>null

            ]

        );

        Platform::updateOrCreate(

            ['name'=>'LoyalFans'],

            [

                'type'=>'usd',

                'multiplier'=>1,

                'conversion_rate'=>null

            ]

        );

    }
}