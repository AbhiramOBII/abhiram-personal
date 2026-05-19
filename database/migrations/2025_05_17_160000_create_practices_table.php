<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('cue')->nullable();
            $table->string('reward')->nullable();
            $table->string('identity_statement')->nullable();
            $table->string('two_minute_version')->nullable();
            $table->string('pillar')->nullable();
            $table->string('hex_color')->default('#4f98a3');
            $table->string('icon_emoji')->default('✅');
            $table->enum('frequency_type', ['daily', 'specific_days'])->default('daily');
            $table->json('frequency_days')->nullable();
            $table->foreignId('stack_after_practice_id')->nullable()->constrained('practices')->nullOnDelete();
            $table->string('stack_trigger')->nullable();
            $table->boolean('is_two_minute_enabled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practices');
    }
};
