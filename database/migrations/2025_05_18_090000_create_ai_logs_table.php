<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('feature', [
                'daily_briefing', 'task_suggestion', 'overload_guard',
                'weekly_insight', 'pattern_insight', 'daily_quote', 'custom',
            ]);
            $table->integer('prompt_tokens')->nullable();
            $table->integer('completion_tokens')->nullable();
            $table->string('model')->default('gpt-4o-mini');
            $table->string('prompt_summary')->nullable();
            $table->string('response_summary')->nullable();
            $table->integer('latency_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
