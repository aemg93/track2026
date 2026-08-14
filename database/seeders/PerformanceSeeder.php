<?php

namespace Database\Seeders;

use App\Enums\WorkShift;
use App\Models\Platform;
use App\Services\RankingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $studioId = DB::table('studios')
            ->where('name', 'Test Studio')
            ->value('id');

        $platforms = Platform::all();

        $performances = [

            [
                'studio_id' => $studioId,
                'user_id' => null,

                'first_name' => 'Camila',
                'last_name' => 'Moreno',
                'nickname' => 'CamilaHot',

                'email' => 'camila@example.com',
                'phone' => '3001234567',

                'country' => 'Colombia',
                'city' => 'Bogotá',
                'address' => 'Calle 100 #10-20',

                'document_type' => 'CC',
                'document_number' => '1000123456',

                'birth_date' => '1998-05-12',
                'profile_photo' => null,

                'active' => true,

                'work_shift' => WorkShift::Night->value,

                'hours_streamed' => 0,
                'ranking_score' => 0,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'studio_id' => $studioId,
                'user_id' => null,

                'first_name' => 'Valentina',
                'last_name' => 'Gomez',
                'nickname' => 'ValeFire',

                'email' => 'valentina@example.com',
                'phone' => '3019876543',

                'country' => 'Colombia',
                'city' => 'Medellin',
                'address' => 'Carrera 45 #20-15',

                'document_type' => 'CC',
                'document_number' => '1000987654',

                'birth_date' => '1997-11-25',
                'profile_photo' => null,

                'active' => true,

                'work_shift' => WorkShift::Afternoon->value,

                'hours_streamed' => 0,
                'ranking_score' => 0,

                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($performances as $performanceData) {

            $performanceId = DB::table('performances')
                ->insertGetId($performanceData);

            foreach ($platforms as $platform) {

                $hours = rand(1, 6);

                $tokens = $hours * rand(50, 200);

                $usd = $platform->type === 'usd'
                    ? $tokens
                    : $tokens * ($platform->conversion_rate ?? 0.05);

                DB::table('performance_platform')->insert([

                    'performance_id' => $performanceId,

                    'platform_id' => $platform->id,

                    'hours_streamed' => $hours,

                    'tokens' => $tokens,

                    'earnings_usd' => round($usd, 2),

                    'multiplier' => $platform->multiplier,

                    'conversion_rate' => $platform->conversion_rate ?? 0.05,

                    'recorded_at' => Carbon::now()
                        ->subDays(rand(0, 10)),

                    'created_at' => now(),

                    'updated_at' => now(),
                ]);
            }

            app(RankingService::class)
                ->recalculate($performanceId);
        }
    }
}