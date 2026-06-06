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

            // Horas trabajadas en la plataforma
            $table->decimal('hours_streamed', 8, 2)->default(0);

            // Ganancias en USD ya calculadas
            $table->decimal('earnings_usd', 12, 2)->default(0);

            // Tokens (si la plataforma trabaja por tokens)
            $table->decimal('tokens', 14, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | SNAPSHOT FINANCIERO (IMPORTANTE)
            |--------------------------------------------------------------------------
            */

            // multiplicador aplicado en ese momento
            $table->decimal('multiplier', 8, 2)->default(1);

            // tasa de conversión token → USD en el momento del registro
            $table->decimal('conversion_rate', 10, 4)->nullable();

            /*
            |--------------------------------------------------------------------------
            | PERFORMANCE
            |--------------------------------------------------------------------------
            */

            // score del modelo en esa plataforma
            $table->decimal('ranking_score', 8, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP DE CORTE
            |--------------------------------------------------------------------------
            */

            // permite snapshots diarios / semanales
            $table->timestamp('recorded_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | RESTRICCIÓN (OPTIMIZADA Y SEGURA)
            |--------------------------------------------------------------------------
            */

            // evita duplicados lógicos sin generar nombres largos en MySQL
            $table->unique(
                ['performance_id', 'platform_id', 'recorded_at'],
                'pp_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_platform');
    }
};