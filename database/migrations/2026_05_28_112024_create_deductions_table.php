<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deductions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('performance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('category'); // juguetes, bebidas, etc
            $table->string('reason');

            $table->decimal('amount', 12, 2);
            $table->date('date');

            // 💳 financiación
            $table->boolean('is_installment')->default(false);
            $table->integer('installments')->nullable();
            $table->decimal('installment_value', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};