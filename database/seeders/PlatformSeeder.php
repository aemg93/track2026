<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Platform;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        Platform::updateOrCreate(
            ['slug' => 'cam4'],
            [
                'name' => 'Cam4',
                'type' => 'token',
                'multiplier' => 2,
                'conversion_rate' => 0.05,
                'is_active' => true,
            ]
        );

        Platform::updateOrCreate(
            ['slug' => 'stripchat'],
            [
                'name' => 'Stripchat',
                'type' => 'token',
                'multiplier' => 1,
                'conversion_rate' => 0.05,
                'is_active' => true,
            ]
        );

        Platform::updateOrCreate(
            ['slug' => 'chaturbate'],
            [
                'name' => 'Chaturbate',
                'type' => 'token',
                'multiplier' => 1,
                'conversion_rate' => 0.05,
                'is_active' => true,
            ]
        );

        Platform::updateOrCreate(
            ['slug' => 'cams'],
            [
                'name' => 'Cams',
                'type' => 'usd',
                'multiplier' => 1,
                'conversion_rate' => 1,
                'is_active' => true,
            ]
        );

        Platform::updateOrCreate(
            ['slug' => 'loyalfans'],
            [
                'name' => 'LoyalFans',
                'type' => 'usd',
                'multiplier' => 1,
                'conversion_rate' => 1,
                'is_active' => true,
            ]
        );
    }
}