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
        Schema::create('kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->decimal('gmv_per_hour', 15, 2)->default(2700000);
            $table->decimal('conversion_rate', 5, 4)->default(0.03);
            $table->decimal('aov', 15, 2)->default(180000);
            $table->integer('likes_per_minute')->default(300);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_targets');
    }
};
