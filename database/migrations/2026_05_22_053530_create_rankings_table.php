<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_id')->constrained('performances')->onDelete('cascade');
            $table->decimal('score', 8, 2);
            $table->integer('position');
            $table->date('calculated_at');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rankings');
    }
};
