<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_plan_id')->constrained('daily_plans')->cascadeOnDelete();
            $table->foreignId('time_block_id')->nullable()->constrained('time_blocks')->nullOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->string('pillar')->nullable();
            $table->enum('priority', ['must', 'should', 'bonus'])->default('should');
            $table->tinyInteger('estimated_minutes')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_rolled_over')->default(false);
            $table->date('rolled_from_date')->nullable();
            $table->tinyInteger('rollover_count')->default(0);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
