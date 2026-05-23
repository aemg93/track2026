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

            $table->foreignId('performance_id')
                ->constrained('performances')
                ->cascadeOnDelete()
                ->unique();

            $table->unsignedTinyInteger('model_percentage')
                ->default(60);

            $table->unsignedTinyInteger('studio_percentage')
                ->default(40);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_splits');
    }
};