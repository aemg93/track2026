<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\EarningStatus;
use App\Enums\ShiftStatus;
use App\Enums\WorkShift;
use App\Models\Earning;
use App\Models\Performance;
use App\Models\Platform;
use App\Models\Shift;
use App\Models\Studio;
use App\Models\User;
use App\Services\EarningService;
use App\Services\FinancialSummaryService;
use App\Services\PerformanceAnalyticsService;
use App\Services\RankingService;
use App\Services\ShiftFinancialSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FinancialDomainTest extends TestCase
{
    use RefreshDatabase;

    private Performance $performance;
    private Platform $platform;

    protected function setUp(): void
    {
        parent::setUp();

        $studio = Studio::create(['name' => 'Financial Test Studio']);
        $user = User::create([
            'name' => 'Financial Test User',
            'email' => uniqid('financial-', true) . '@test.com',
            'password' => 'password',
        ]);

        $this->performance = Performance::create([
            'studio_id' => $studio->id,
            'user_id' => $user->id,
            'first_name' => 'Test',
            'last_name' => 'Performance',
            'nickname' => 'financial-test',
            'email' => uniqid('performance-', true) . '@test.com',
            'phone' => '3000000000',
            'country' => 'Colombia',
            'city' => 'Bogota',
            'address' => 'Test',
            'document_type' => 'CC',
            'document_number' => uniqid('doc-', true),
            'birth_date' => '1998-01-01',
            'active' => true,
            'work_shift' => WorkShift::Morning,
            'hours_streamed' => 0,
            'ranking_score' => 0,
        ]);

        $this->platform = Platform::create([
            'name' => 'Financial Platform',
            'slug' => uniqid('financial-platform-', true),
        ]);
    }

    public function test_financial_summary_only_includes_approved_and_paid(): void
    {
        foreach ([
            EarningStatus::Draft,
            EarningStatus::Cancelled,
            EarningStatus::Approved,
            EarningStatus::Paid,
        ] as $status) {
            $this->earning($status, $status === EarningStatus::Paid ? now() : null);
        }

        $summary = app(FinancialSummaryService::class)->summary($this->performance);

        $this->assertSame(20.0, $summary['gross_usd']);
    }

    #[DataProvider('invalidPaidAtStatuses')]
    public function test_non_paid_statuses_cannot_have_paid_at(EarningStatus $status): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->earning($status, now());
    }

    public static function invalidPaidAtStatuses(): array
    {
        return [
            'draft' => [EarningStatus::Draft],
            'approved' => [EarningStatus::Approved],
            'cancelled' => [EarningStatus::Cancelled],
        ];
    }

    public function test_paid_requires_paid_at(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->earning(EarningStatus::Paid);
    }

    public function test_sync_does_not_modify_closed_earnings_and_processes_approved(): void
    {
        $paid = $this->earning(EarningStatus::Paid, now(), 10);
        $cancelled = $this->earning(EarningStatus::Cancelled, null, 20);
        $approved = $this->earning(EarningStatus::Approved, null, 30);

        app(EarningService::class)->syncEarning($paid);
        app(EarningService::class)->syncEarning($cancelled);
        app(EarningService::class)->syncEarning($approved);

        $this->assertSame('10.00', $paid->fresh()->net_usd);
        $this->assertSame('20.00', $cancelled->fresh()->net_usd);
        $this->assertSame('30.00', $approved->fresh()->net_usd);
    }

    public function test_shift_summary_filters_statuses_and_includes_both_boundaries(): void
    {
        $shift = $this->shift('2026-02-15 22:00:00', '2026-02-16 06:00:00');
        $this->earning(EarningStatus::Draft, null, 1, '2026-02-15 22:00:00');
        $this->earning(EarningStatus::Cancelled, null, 2, '2026-02-16 06:00:00');
        $this->earning(EarningStatus::Approved, null, 3, '2026-02-15 22:00:00');
        $this->earning(EarningStatus::Paid, now(), 4, '2026-02-16 06:00:00');

        $summary = app(ShiftFinancialSummaryService::class)->summary($shift);

        $this->assertSame(7.0, $summary['gross_usd']);
        $this->assertSame(7.0, $summary['total_tokens']);
    }

    public function test_ranking_uses_worked_seconds_from_shifts(): void
    {
        $this->performance->update(['hours_streamed' => 999]);
        $this->shift('2026-02-15 09:00:00', '2026-02-15 10:00:00', 3600);
        $this->performance->earnings()->create($this->earningAttributes(EarningStatus::Approved, null, 100));

        app(RankingService::class)->recalculate($this->performance->id);

        $this->assertSame(1, $this->performance->fresh()->hours_streamed);
    }

    public function test_analytics_uses_earnings_for_financial_usd(): void
    {
        $this->performance->platforms()->attach($this->platform->id, [
            'hours_streamed' => 2,
            'earnings_usd' => 9999,
            'tokens' => 100,
            'recorded_at' => now(),
        ]);
        $this->earning(EarningStatus::Draft, null, 7);
        $this->earning(EarningStatus::Approved, null, 12);

        $summary = app(PerformanceAnalyticsService::class)->summary($this->performance);

        $this->assertSame(12.0, $summary['totals']['usd']);
        $this->assertSame(2.0, $summary['platforms'][0]['metrics']['hours']);
        $this->assertSame(12.0, $summary['platforms'][0]['metrics']['usd']);
    }

    private function earning(
        EarningStatus $status,
        mixed $paidAt = null,
        float $gross = 10,
        string $earnedAt = '2026-02-15 23:00:00'
    ): Earning {
        return Earning::create([
            ...$this->earningAttributes($status, $paidAt, $gross),
            'earned_at' => $earnedAt,
        ]);
    }

    private function earningAttributes(EarningStatus $status, mixed $paidAt, float $gross): array
    {
        return [
            'performance_id' => $this->performance->id,
            'platform_id' => $this->platform->id,
            'user_id' => $this->performance->user_id,
            'earned_at' => '2026-02-15 23:00:00',
            'original_amount' => $gross,
            'original_currency' => 'usd',
            'real_tokens' => $gross,
            'gross_usd' => $gross,
            'net_usd' => $gross,
            'model_percentage' => 60,
            'studio_percentage' => 40,
            'model_share_usd' => $gross * .6,
            'studio_share_usd' => $gross * .4,
            'status' => $status,
            'paid_at' => $paidAt,
        ];
    }

    private function shift(string $start, string $end, int $workedSeconds = 0): Shift
    {
        return Shift::create([
            'performance_id' => $this->performance->id,
            'studio_id' => $this->performance->studio_id,
            'started_at' => $start,
            'last_resumed_at' => $start,
            'ended_at' => $end,
            'worked_seconds' => $workedSeconds,
            'total_paused_seconds' => 0,
            'status' => ShiftStatus::Finished,
        ]);
    }
}
