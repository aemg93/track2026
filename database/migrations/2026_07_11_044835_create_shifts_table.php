<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('performance_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('studio_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Lifecycle
            |--------------------------------------------------------------------------
            */

            $table->timestamp('started_at');

            /*
            |--------------------------------------------------------------------------
            | Momento desde el que el turno está corriendo actualmente.
            | Se inicializa al iniciar el turno y se actualiza en cada resume().
            |--------------------------------------------------------------------------
            */

            $table->timestamp('last_resumed_at');

            /*
            |--------------------------------------------------------------------------
            | Si el turno está pausado, aquí queda registrado cuándo inició
            | la pausa actual.
            |--------------------------------------------------------------------------
            */

            $table->timestamp('paused_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Fecha de finalización.
            |--------------------------------------------------------------------------
            */

            $table->timestamp('ended_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Time
            |--------------------------------------------------------------------------
            */

            /*
             * Tiempo total acumulado en pausas.
             */
            $table->unsignedInteger('total_paused_seconds')
                ->default(0);

            /*
             * Tiempo real trabajado acumulado.
             */
            $table->unsignedInteger('worked_seconds')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->string('status', 20)
                ->default('active');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('status');

            $table->index([
                'status',
                'started_at',
            ]);

            $table->index([
                'studio_id',
                'status',
                'started_at',
            ]);

            $table->index([
                'performance_id',
                'status',
            ]);

            $table->index([
                'performance_id',
                'started_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};