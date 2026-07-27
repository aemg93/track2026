<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {

            $table->foreignId('monitor_shift_id')
                ->nullable()
                ->after('user_id')
                ->constrained('monitor_shifts')
                ->nullOnDelete();


            $table->index(
                'monitor_shift_id'
            );

        });
    }


    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {

            $table->dropForeign([
                'monitor_shift_id'
            ]);


            $table->dropIndex([
                'monitor_shift_id'
            ]);


            $table->dropColumn(
                'monitor_shift_id'
            );

        });
    }
};