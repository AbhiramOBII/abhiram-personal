<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_days', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_number')->unique();
            $table->string('day_name');
            $table->string('theme');
            $table->string('theme_short')->nullable();
            $table->string('color');
            $table->string('hex_color', 7);
            $table->string('icon_emoji')->default('📅');
            $table->text('description')->nullable();
            $table->json('pillars')->nullable();
            $table->json('suggested_task_types')->nullable();
            $table->enum('energy_profile', ['low', 'medium', 'high', 'creative', 'social'])->default('medium');
            $table->string('upskill_focus')->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_days');
    }
};
