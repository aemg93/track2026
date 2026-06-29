<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {

            $table->id();


            $table->foreignId('performance_id')
                ->constrained('performances')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->string('reason')->nullable();

            $table->decimal('amount', 12, 2)->default(0);

       
            $table->date('date')->nullable();

        

            $table->timestamps();

            $table->index(['performance_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};