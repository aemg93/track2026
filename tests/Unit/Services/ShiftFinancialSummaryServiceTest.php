<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\ShiftStatus;
use App\Models\Bonus;
use App\Models\Deduction;
use App\Models\Earning;
use App\Models\Penalty;
use App\Models\Performance;
use App\Models\Platform;
use App\Models\Shift;
use App\Models\Studio;
use App\Models\User;
use App\Services\ShiftFinancialSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftFinancialSummaryServiceTest extends TestCase
{
    use RefreshDatabase;

    private Studio $studio;

    private User $user;

    private Performance $performance;

    private Platform $chaturbate;

    private Platform $cam4;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * ---------------------------------------------------------
         * STUDIO
         * ---------------------------------------------------------
         *
         * No asumimos que exista studio_id = 1.
         * Creamos el Studio real dentro de la BD de testing.
         */
        $this->studio = Studio::query()->create([
            'name' => 'Studio Test',
        ]);

        /*
         * ---------------------------------------------------------
         * USER
         * ---------------------------------------------------------
         *
         * No asumimos que exista user_id = 1.
         */
        $this->user = User::query()->create([
            'name' => 'Usuario Test',
            'email' => 'shift-financial-' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);

        /*
         * ---------------------------------------------------------
         * PERFORMANCE
         * ---------------------------------------------------------
         *
         * La Performance utiliza los IDs reales creados arriba.
         */
        $this->performance = Performance::query()->create([
            'studio_id' => $this->studio->id,
            'user_id' => $this->user->id,

            'first_name' => 'Camila',
            'last_name' => 'Moreno',
            'nickname' => 'CamilaHot',

            'email' => 'camila-' . uniqid() . '@test.com',
            'phone' => '3000000000',

            'country' => 'Colombia',
            'city' => 'Bogotá',
            'address' => 'Test',

            'document_type' => 'CC',
            'document_number' => 'TEST-' . uniqid(),

            'birth_date' => '1998-05-12',

            'active' => true,
            'work_shift' => 'night',

            'hours_streamed' => 0,
            'ranking_score' => 0,
        ]);

        /*
         * ---------------------------------------------------------
         * PLATFORMS
         * ---------------------------------------------------------
         *
         * RefreshDatabase deja la BD limpia.
         *
         * Por eso creamos las plataformas necesarias.
         *
         * La tabla platforms exige slug.
         */
        $this->chaturbate = Platform::query()->firstOrCreate(
            ['slug' => 'chaturbate'],
            [
                'name' => 'Chaturbate',
            ]
        );

        $this->cam4 = Platform::query()->firstOrCreate(
            ['slug' => 'cam4'],
            [
                'name' => 'Cam4',
            ]
        );
    }

    /**
     * Crea un Shift terminado.
     */
    private function createShift(
        string $start,
        string $end
    ): Shift {
        return Shift::query()->create([
            'performance_id' => $this->performance->id,
            'studio_id' => $this->studio->id,

            'started_at' => $start,
            'last_resumed_at' => $start,

            'paused_at' => null,
            'ended_at' => $end,

            'total_paused_seconds' => 0,
            'worked_seconds' => 0,

            'status' => ShiftStatus::Finished,
        ]);
    }

    /**
     * Crea un earning para la Performance del test.
     */
    private function createEarning(
        Platform $platform,
        string $earnedAt,
        float $originalAmount,
        float $realTokens,
        float $grossUsd,
        float $modelShareUsd = 0,
        float $studioShareUsd = 0
    ): Earning {
        return Earning::query()->create([
            /*
             * Relaciones obligatorias.
             */
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,
            'platform_id' => $platform->id,

            /*
             * Momento en que se produjo el earning.
             */
            'earned_at' => $earnedAt,

            /*
             * Información original de la producción.
             */
            'original_amount' => $originalAmount,
            'original_currency' => 'tokens',

            /*
             * Tokens reales utilizados para el cálculo.
             */
            'real_tokens' => $realTokens,

            /*
             * Conversión.
             */
            'conversion_rate' => 1,
            'multiplier' => 1,

            /*
             * Valores financieros.
             */
            'gross_usd' => $grossUsd,

            'bonus_usd' => 0,
            'penalty_usd' => 0,
            'deduction_usd' => 0,

            'net_usd' => $grossUsd,

            /*
             * Distribución.
             */
            'model_percentage' => 60,
            'studio_percentage' => 40,

            'model_share_usd' => $modelShareUsd,
            'studio_share_usd' => $studioShareUsd,

            /*
             * Estado financiero.
             */
            'status' => 'approved',
        ]);
    }

    /**
     * Un earning producido dentro del intervalo del Shift
     * debe ser incluido.
     */
    public function test_earnings_inside_shift_are_included(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        $this->createEarning(
            $this->chaturbate,
            '2026-02-15 01:00:00',
            6000,
            6000,
            300,
            180,
            120
        );

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            300.0,
            $summary['gross_usd']
        );

        $this->assertSame(
            6000.0,
            $summary['total_tokens']
        );
    }

    /**
     * Un earning producido después del Shift debe ser excluido.
     */
    public function test_earnings_outside_shift_are_excluded(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        $this->createEarning(
            $this->chaturbate,
            '2026-02-15 07:00:00',
            6000,
            6000,
            300,
            180,
            120
        );

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            0.0,
            $summary['gross_usd']
        );

        $this->assertSame(
            0.0,
            $summary['total_tokens']
        );

        $this->assertEmpty(
            $summary['platforms']
        );
    }

    /**
     * El bono dentro del periodo se incluye y el bono
     * fuera del periodo se excluye.
     */
    public function test_bonus_inside_shift_is_included_and_bonus_outside_is_excluded(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        Bonus::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'reason' => 'Bono dentro',
            'amount' => 50,
            'date' => '2026-02-15',
        ]);

        Bonus::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'reason' => 'Bono fuera',
            'amount' => 100,
            'date' => '2026-02-16',
        ]);

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            50.0,
            $summary['bonus_usd']
        );
    }

    /**
     * La penalización dentro del periodo se incluye y la
     * penalización fuera del periodo se excluye.
     */
    public function test_penalty_inside_shift_is_included_and_penalty_outside_is_excluded(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        Penalty::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'reason' => 'Penalización dentro',
            'amount' => 20,
            'date' => '2026-02-15',
        ]);

        Penalty::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'reason' => 'Penalización fuera',
            'amount' => 100,
            'date' => '2026-02-16',
        ]);

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            20.0,
            $summary['penalty_usd']
        );
    }

    /**
     * La deducción dentro del periodo se incluye y la
     * deducción fuera del periodo se excluye.
     */
    public function test_deduction_inside_shift_is_included_and_deduction_outside_is_excluded(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        Deduction::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'category' => 'test',
            'reason' => 'Deducción dentro',

            'amount' => 30,
            'date' => '2026-02-15',

            'is_installment' => false,
            'installments' => null,
            'installment_value' => null,
        ]);

        Deduction::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'category' => 'test',
            'reason' => 'Deducción fuera',

            'amount' => 100,
            'date' => '2026-02-16',

            'is_installment' => false,
            'installments' => null,
            'installment_value' => null,
        ]);

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            30.0,
            $summary['deduction_usd']
        );
    }

    /**
     * Verifica el resultado financiero y la distribución
     * entre modelo y estudio.
     */
    public function test_net_and_model_studio_distribution_are_correct(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        $this->createEarning(
            $this->chaturbate,
            '2026-02-15 02:00:00',
            6000,
            6000,
            550,
            330,
            220
        );

        Bonus::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'reason' => 'Bono',
            'amount' => 50,
            'date' => '2026-02-15',
        ]);

        Penalty::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'reason' => 'Penalización',
            'amount' => 20,
            'date' => '2026-02-15',
        ]);

        Deduction::query()->create([
            'performance_id' => $this->performance->id,
            'user_id' => $this->user->id,

            'category' => 'test',
            'reason' => 'Deducción',

            'amount' => 30,
            'date' => '2026-02-15',

            'is_installment' => false,
            'installments' => null,
            'installment_value' => null,
        ]);

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            550.0,
            $summary['gross_usd']
        );

        $this->assertSame(
            50.0,
            $summary['bonus_usd']
        );

        $this->assertSame(
            20.0,
            $summary['penalty_usd']
        );

        $this->assertSame(
            30.0,
            $summary['deduction_usd']
        );

        $this->assertSame(
            550.0,
            $summary['net_usd']
        );

        $this->assertSame(
            60.0,
            $summary['model_percentage']
        );

        $this->assertSame(
            40.0,
            $summary['studio_percentage']
        );

        $this->assertSame(
            330.0,
            $summary['model_share_usd']
        );

        $this->assertSame(
            220.0,
            $summary['studio_share_usd']
        );
    }

    /**
     * Un earning exactamente al inicio pertenece al Shift.
     */
    public function test_earning_exactly_at_started_at_is_included(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        $this->createEarning(
            $this->chaturbate,
            '2026-02-14 22:00:00',
            1000,
            1000,
            50,
            30,
            20
        );

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            50.0,
            $summary['gross_usd']
        );

        $this->assertSame(
            1000.0,
            $summary['total_tokens']
        );
    }

    /**
     * Un earning exactamente al final pertenece al Shift.
     */
    public function test_earning_exactly_at_ended_at_is_included(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        $this->createEarning(
            $this->chaturbate,
            '2026-02-15 06:00:00',
            1000,
            1000,
            50,
            30,
            20
        );

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            50.0,
            $summary['gross_usd']
        );

        $this->assertSame(
            1000.0,
            $summary['total_tokens']
        );
    }

    /**
     * Verifica agrupación de producción por plataforma.
     */
    public function test_platforms_and_tokens_are_grouped_correctly(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        $this->createEarning(
            $this->chaturbate,
            '2026-02-15 01:00:00',
            6000,
            6000,
            300,
            180,
            120
        );

        $this->createEarning(
            $this->cam4,
            '2026-02-15 02:00:00',
            2500,
            5000,
            250,
            150,
            100
        );

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        $this->assertSame(
            11000.0,
            $summary['total_tokens']
        );

        $this->assertCount(
            2,
            $summary['platforms']
        );

        $chaturbate = collect(
            $summary['platforms']
        )->firstWhere(
            'platform_name',
            'Chaturbate'
        );

        $cam4 = collect(
            $summary['platforms']
        )->firstWhere(
            'platform_name',
            'Cam4'
        );

        $this->assertNotNull($chaturbate);
        $this->assertNotNull($cam4);

        $this->assertSame(
            6000.0,
            $chaturbate['real_tokens']
        );

        $this->assertSame(
            300.0,
            $chaturbate['gross_usd']
        );

        $this->assertSame(
            5000.0,
            $cam4['real_tokens']
        );

        $this->assertSame(
            250.0,
            $cam4['gross_usd']
        );
    }

    /**
     * Verifica un Shift nocturno que cruza medianoche.
     *
     * Deben incluirse únicamente los earnings entre:
     *
     * 2026-02-14 22:00:00
     * 2026-02-15 06:00:00
     */
    public function test_night_shift_crossing_midnight_uses_exact_time_interval(): void
    {
        $shift = $this->createShift(
            '2026-02-14 22:00:00',
            '2026-02-15 06:00:00'
        );

        /*
         * Fuera: un segundo antes del inicio.
         */
        $this->createEarning(
            $this->chaturbate,
            '2026-02-14 21:59:59',
            1000,
            1000,
            50,
            30,
            20
        );

        /*
         * Dentro: exactamente al inicio.
         */
        $this->createEarning(
            $this->chaturbate,
            '2026-02-14 22:00:00',
            2000,
            2000,
            100,
            60,
            40
        );

        /*
         * Dentro: durante el Shift.
         */
        $this->createEarning(
            $this->chaturbate,
            '2026-02-15 03:00:00',
            3000,
            3000,
            150,
            90,
            60
        );

        /*
         * Dentro: exactamente al final.
         */
        $this->createEarning(
            $this->chaturbate,
            '2026-02-15 06:00:00',
            4000,
            4000,
            200,
            120,
            80
        );

        /*
         * Fuera: un segundo después del final.
         */
        $this->createEarning(
            $this->chaturbate,
            '2026-02-15 06:00:01',
            5000,
            5000,
            250,
            150,
            100
        );

        $summary = app(
            ShiftFinancialSummaryService::class
        )->summary($shift);

        /*
         * Dentro:
         *
         * 100 + 150 + 200 = 450 USD
         *
         * Tokens:
         *
         * 2000 + 3000 + 4000 = 9000
         */
        $this->assertSame(
            450.0,
            $summary['gross_usd']
        );

        $this->assertSame(
            9000.0,
            $summary['total_tokens']
        );
    }
}
