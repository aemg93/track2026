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

            $table->foreignId('performance_id')
                ->constrained('performances')
                ->cascadeOnDelete();

            $table->foreignId('platform_id')
                ->constrained('platforms')
                ->cascadeOnDelete();

            $table->timestamp('earned_at');

            $table->decimal('original_amount', 14, 2);

            $table->enum('original_currency', [
                'usd',
                'tokens',
            ]);

            $table->decimal('real_tokens', 14, 2)
                ->nullable();

            $table->decimal('conversion_rate', 12, 6)
                ->default(1);

            $table->decimal('multiplier', 10, 4)
                ->default(1);

            $table->decimal('gross_usd', 14, 2);

            $table->decimal('bonus_usd', 14, 2)
                ->default(0);

            $table->decimal('penalty_usd', 14, 2)
                ->default(0);

            $table->decimal('deduction_usd', 14, 2)
                ->default(0);

            $table->decimal('net_usd', 14, 2)
                ->default(0);

            $table->decimal('model_percentage', 5, 2)
                ->default(60);

            $table->decimal('studio_percentage', 5, 2)
                ->default(40);

            $table->decimal('model_share_usd', 14, 2)
                ->default(0);

            $table->decimal('studio_share_usd', 14, 2)
                ->default(0);

            $table->enum('status', [
                'draft',
                'approved',
                'paid',
                'cancelled',
            ])->default('draft');

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'performance_id',
                'earned_at',
            ]);

            $table->index([
                'platform_id',
                'earned_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};