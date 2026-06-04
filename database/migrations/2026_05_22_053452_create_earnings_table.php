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

            // 🔗 Modelo (a qué performer pertenece la ganancia)
            $table->foreignId('performance_id')
                ->constrained('performances')
                ->cascadeOnDelete();

            // 🔗 Plataforma de origen del ingreso
            $table->foreignId('platform_id')
                ->constrained('platforms')
                ->cascadeOnDelete();

            // 💰 Datos financieros
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_usd', 12, 2)->nullable();

            $table->date('date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};