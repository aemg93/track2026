<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | IDENTIFICACIÓN
            |--------------------------------------------------------------------------
            */

            $table->string('name');

            // chaturbate
            // stripchat
            // cam4
            // bongacams
            $table->string('slug')->unique();

            /*
            |--------------------------------------------------------------------------
            | CONFIGURACIÓN FINANCIERA
            |--------------------------------------------------------------------------
            */

            // token = trabaja por tokens
            // usd   = reporta USD directamente
            $table->enum('type', [
                'token',
                'usd',
            ]);

            /*
            |--------------------------------------------------------------------------
            | CONVERSIÓN
            |--------------------------------------------------------------------------
            */

            // multiplicador propio de la plataforma
            $table->decimal(
                'multiplier',
                8,
                2
            )->default(1);

            // valor de token en USD
            // Ej:
            // 0.05
            // 0.08
            $table->decimal(
                'conversion_rate',
                10,
                4
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | PRESENTACIÓN
            |--------------------------------------------------------------------------
            */

            $table->string('logo')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};