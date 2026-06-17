<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('performance_platform', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELACIONES
            |--------------------------------------------------------------------------
            */

            $table->foreignId('performance_id')
                ->constrained('performances')
                ->cascadeOnDelete();

            $table->foreignId('platform_id')
                ->constrained('platforms')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | MÉTRICAS OPERATIVAS
            |--------------------------------------------------------------------------
            */

            $table->decimal('hours_streamed', 8, 2)
                ->default(0);

            $table->decimal('tokens', 14, 2)
                ->default(0);

            $table->decimal('earnings_usd', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | SNAPSHOT FINANCIERO
            |--------------------------------------------------------------------------
            */

            $table->decimal('multiplier', 8, 2)
                ->default(1);

            $table->decimal('conversion_rate', 10, 4)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP DE CORTE
            |--------------------------------------------------------------------------
            */

            $table->timestamp('recorded_at')
                ->useCurrent()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | AUDITORÍA
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | SNAPSHOT ÚNICO
            |--------------------------------------------------------------------------
            */

            $table->unique(
                [
                    'performance_id',
                    'platform_id',
                    'recorded_at'
                ],
                'pp_unique'
            );

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES ANALÍTICOS
            |--------------------------------------------------------------------------
            */

            $table->index([
                'performance_id',
                'platform_id'
            ]);

            $table->index([
                'platform_id',
                'recorded_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_platform');
    }
};