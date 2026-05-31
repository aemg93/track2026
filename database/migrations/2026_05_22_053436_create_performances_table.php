<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('studio_id')
                ->constrained('studios')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('nickname')->nullable();

            $table->string('email')->unique();
            $table->string('phone')->nullable();

            $table->string('country')->nullable();
            $table->string('city')->nullable();

            $table->text('address')->nullable();

            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();

            $table->date('birth_date');

            $table->string('profile_photo')->nullable();

            $table->boolean('active')
                ->default(true);

            $table->integer('hours_streamed')
                ->default(0);

            $table->decimal('ranking_score', 8, 2)
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};