<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
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

            $table->timestamp('started_at');

            $table->timestamp('ended_at')
                ->nullable();

            $table->enum('status', [
                'active',
                'paused',
                'finished',
            ])->default('active');

            $table->timestamps();

            $table->index('status');
            $table->index('started_at');
            $table->index('ended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};