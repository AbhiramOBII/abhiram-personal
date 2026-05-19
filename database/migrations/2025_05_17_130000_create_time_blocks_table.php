<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('working_day_id')->constrained('working_days')->cascadeOnDelete();
            $table->string('name');
            $table->enum('block_type', ['work', 'break', 'free', 'recovery'])->default('free');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('intent')->nullable();
            $table->tinyInteger('capacity')->default(4);
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_blocks');
    }
};
