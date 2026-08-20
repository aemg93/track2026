<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Earning;
use App\Models\Performance;
use App\Models\Platform;
use App\Models\Studio;
use App\Models\User;
use App\Services\MonitorShiftActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorShiftActivityServiceTest extends TestCase
{
    use RefreshDatabase;

    private Studio $studio;
    private User $user;
    private Performance $ana;
    private Performance $camila;
    private Platform $chaturbate;
    private Platform $cam4;
    private Platform $stripchat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studio = Studio::query()->create([
            'name' => 'Studio Test',
        ]);

        $this->user = User::query()->create([
            'name' => 'Usuario Test',
            'email' => 'monitor-activity-' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->chaturbate = Platform::query()->create([
            'name' => 'Chaturbate',
            'slug' => 'chaturbate',
        ]);

        $this->cam4 = Platform::query()->create([
            'name' => 'Cam4',
            'slug' => 'cam4',
        ]);

        $this->stripchat = Platform::query()->create([
            'name' => 'Stripchat',
            'slug' => 'stripchat',
        ]);

        $this->ana = Performance::query()->create([
            'studio_id' => $this->studio->id,
            'user_id' => $this->user->id,
            'first_name' => 'Ana María',
            'last_name' => 'López Oropeza',
            'nickname' => 'AnaMaria',
            'email' => 'ana-' . uniqid() . '@test.com',
            'phone' => '3000000001',
            'country' => 'Colombia',
            'city' => 'Bucaramanga',
            'address' => 'Test',
            'document_type' => 'CC',
            'document_number' => 'ANA-' . uniqid(),
            'birth_date' => '1998-05-12',
            'active' => true,
            'work_shift' => 'morning',
            'hours_streamed' => 0,
            'ranking_score' => 0,
        ]);

        $this->camila = Performance::query()->create([
            'studio_id' => $this->studio->id,
            'user_id' => $this->user->id,
            'first_name' => 'Camila',
            'last_name' => 'Moreno',
            'nickname' => 'CamilaHot',
            'email' => 'camila-' . uniqid() . '@test.com',
            'phone' => '3000000002',
            'country' => 'Colombia',
            'city' => 'Bucaramanga',
            'address' => 'Test',
            'document_type' => 'CC',
            'document_number' => 'CAM-' . uniqid(),
            'birth_date' => '1998-05-12',
            'active' => true,
            'work_shift' => 'night',
            'hours_streamed' => 0,
            'ranking_score' => 0,
        ]);
    }

    private function createEarning(
        Performance $performance,
        Platform $platform,
        string $earnedAt,
        float $tokens,
        float $grossUsd
    ): Earning {
        return Earning::query()->create([
            'performance_id' => $performance->id,
            'user_id' => $this->user->id,
            'platform_id' => $platform->id,
            'earned_at' => $earnedAt,
            'original_amount' => $tokens,
            'original_currency' => 'tokens',
            'real_tokens' => $tokens,
            'conversion_rate' => 1,
            'multiplier' => 1,
            'gross_usd' => $grossUsd,
            'bonus_usd' => 0,
            'penalty_usd' => 0,
            'deduction_usd' => 0,
            'net_usd' => $grossUsd,
            'model_percentage' => 60,
            'studio_percentage' => 40,
            'model_share_usd' => $grossUsd * 0.60,
            'studio_share_usd' => $grossUsd * 0.40,
            'status' => 'approved',
        ]);
    }

    private function summary(): array
    {
        return app(MonitorShiftActivityService::class)
            ->summary($this->studio->id);
    }

    private function findModel(array $summary, int $performanceId): ?array
    {
        return collect($summary['models'] ?? [])
            ->first(
                fn (array $model): bool =>
                    ($model['performance_id'] ?? null) === $performanceId
            );
    }

    public function test_model_belongs_to_single_work_shift_regardless_of_earning_time(): void
    {
        $this->createEarning(
            $this->ana,
            $this->chaturbate,
            '2026-08-17 22:45:00',
            2000,
            100
        );

        $this->createEarning(
            $this->ana,
            $this->cam4,
            '2026-08-18 02:30:00',
            3000,
            150
        );

        $this->createEarning(
            $this->ana,
            $this->stripchat,
            '2026-08-18 07:45:00',
            1500,
            75
        );

        $this->createEarning(
            $this->camila,
            $this->cam4,
            '2026-08-18 01:00:00',
            4000,
            200
        );

        $summary = $this->summary();

        $this->assertArrayHasKey('models', $summary);

        $ana = $this->findModel($summary, $this->ana->id);
        $camila = $this->findModel($summary, $this->camila->id);

        $this->assertNotNull($ana);
        $this->assertNotNull($camila);

        $this->assertSame('morning', $ana['work_shift']);
        $this->assertSame(6500.0, (float) $ana['tokens']);

        $this->assertSame('night', $camila['work_shift']);
        $this->assertSame(4000.0, (float) $camila['tokens']);
    }

    public function test_all_platforms_are_totalized_into_one_model_total(): void
    {
        $this->createEarning(
            $this->ana,
            $this->chaturbate,
            '2026-08-18 00:30:00',
            2000,
            100
        );

        $this->createEarning(
            $this->ana,
            $this->cam4,
            '2026-08-18 01:30:00',
            3000,
            150
        );

        $this->createEarning(
            $this->ana,
            $this->stripchat,
            '2026-08-18 03:30:00',
            1500,
            75
        );

        $summary = $this->summary();

        $anaModels = collect($summary['models'] ?? [])
            ->filter(
                fn (array $model): bool =>
                    ($model['performance_id'] ?? null) === $this->ana->id
            );

        $this->assertCount(1, $anaModels);

        $ana = $anaModels->first();

        $this->assertSame(6500.0, (float) $ana['tokens']);
        $this->assertSame('morning', $ana['work_shift']);
    }

    public function test_work_shift_is_not_changed_by_earning_time(): void
    {
        $this->createEarning(
            $this->ana,
            $this->chaturbate,
            '2026-08-17 22:30:00',
            1000,
            50
        );

        $this->createEarning(
            $this->ana,
            $this->cam4,
            '2026-08-18 02:00:00',
            2000,
            100
        );

        $this->createEarning(
            $this->ana,
            $this->stripchat,
            '2026-08-18 05:59:59',
            3000,
            150
        );

        $this->createEarning(
            $this->ana,
            $this->chaturbate,
            '2026-08-18 08:00:00',
            4000,
            200
        );

        $summary = $this->summary();

        $ana = $this->findModel($summary, $this->ana->id);

        $this->assertNotNull($ana);
        $this->assertSame('morning', $ana['work_shift']);
        $this->assertSame(10000.0, (float) $ana['tokens']);
    }
}