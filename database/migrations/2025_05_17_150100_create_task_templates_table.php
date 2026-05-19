<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('working_day_id')->nullable()->constrained('working_days')->nullOnDelete();
            $table->foreignId('time_block_id')->nullable()->constrained('time_blocks')->nullOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->string('pillar')->nullable();
            $table->enum('priority', ['must', 'should', 'bonus'])->default('should');
            $table->tinyInteger('estimated_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_templates');
    }
};
