<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->text('notes')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        Schema::create('calendar_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_day_id')->constrained('calendar_days')->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('description');
            $table->string('pillar');
            $table->boolean('is_completed')->default(false);
            $table->integer('sort_order')->default(0);
            $table->foreignId('source_time_slot_id')->nullable()->constrained('time_slots')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_tasks');
        Schema::dropIfExists('calendar_days');
    }
};
