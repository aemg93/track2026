<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | PERFORMANCE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('performance_id')
                ->constrained('performances')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PERIODO LIQUIDADO
            |--------------------------------------------------------------------------
            */

            $table->date('period_start');
            $table->date('period_end');

            /*
            |--------------------------------------------------------------------------
            | INGRESOS Y AJUSTES
            |--------------------------------------------------------------------------
            */

            $table->decimal('gross_usd', 12, 2)->default(0);

            $table->decimal('bonus_usd', 12, 2)->default(0);
            $table->decimal('penalty_usd', 12, 2)->default(0);
            $table->decimal('deduction_usd', 12, 2)->default(0);

            $table->decimal('net_usd', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | SPLIT
            |--------------------------------------------------------------------------
            */

            $table->decimal('model_percentage', 5, 2)->default(60);
            $table->decimal('studio_percentage', 5, 2)->default(40);

            $table->decimal('model_share_usd', 12, 2)->default(0);
            $table->decimal('studio_share_usd', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | ESTADO DEL CIERRE
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'draft',
                'pending',
                'approved',
                'paid',
                'cancelled'
            ])->default('draft');

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index([
                'performance_id',
                'period_start',
                'period_end'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};