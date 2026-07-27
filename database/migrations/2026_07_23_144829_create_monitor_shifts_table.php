<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitor_shifts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('monitor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('studio_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamp('started_at');

            $table->timestamp('ended_at')
                ->nullable();

            $table->enum(
                'status',
                [
                    'active',
                    'finished',
                ]
            )
            ->default('active');

            $table->timestamps();

            $table->index(
                [
                    'monitor_id',
                    'status'
                ]
            );


            $table->index(
                [
                    'studio_id',
                    'started_at'
                ]
            );

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_shifts');
    }
};