<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        Schema::create('performances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('studio_id')
                  ->constrained('studios')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('name');

            $table->boolean('active')
                  ->default(true);

            $table->integer('hours_streamed')
                  ->default(0);

            $table->decimal('ranking_score',8,2)
                  ->default(0);

            $table->timestamps();
        });

    }

    public function down(): void {

        Schema::dropIfExists('performances');

    }
};