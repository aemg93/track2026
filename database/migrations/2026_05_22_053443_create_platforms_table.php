<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {

        Schema::create('platforms', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            // ¿la plataforma paga en tokens o USD?
            $table->enum(
                'type',
                ['token','usd']
            );

            // Cam4=2
            // Chaturbate=1
            // Stripchat=1
            $table->decimal(
                'multiplier',
                8,
                2
            )->default(1);

            // valor token en USD
            // 0.05
            // 0.08
            $table->decimal(
                'conversion_rate',
                8,
                4
            )->nullable();

            $table->timestamps();

        });

    }

    public function down(): void {

        Schema::dropIfExists(
            'platforms'
        );

    }
};