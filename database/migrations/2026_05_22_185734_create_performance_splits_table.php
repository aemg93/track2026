<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_splits', function (Blueprint $table) {

            $table->id();

            /**
             * Relación 1:1 con modelo
             */
            $table->foreignId('performance_id')
                ->constrained('performances')
                ->cascadeOnDelete()
                ->unique();

            /**
             * Porcentaje del modelo
             * (ej: 70 = 70%)
             */
            $table->unsignedTinyInteger('model_percentage')
                ->default(60);

            /**
             * Porcentaje del estudio
             * (ej: 30 = 30%)
             */
            $table->unsignedTinyInteger('studio_percentage')
                ->default(40);

            /**
             * Estado del split
             * útil para futuras versiones (cambio de contrato)
             */
            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            /**
             * Nota importante:
             * model_percentage + studio_percentage debe ser = 100
             * (validación se maneja en Service o Model Observer)
             */
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_splits');
    }
};