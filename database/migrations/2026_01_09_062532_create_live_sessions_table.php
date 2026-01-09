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
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hosts')->onDelete('cascade');
            $table->date('date');
            $table->float('hours_live', 8, 2)->default(0);
            $table->decimal('gmv', 15, 2)->default(0);
            $table->integer('orders')->default(0);
            $table->integer('viewers')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('errors')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};
