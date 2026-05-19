<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained('practices')->cascadeOnDelete();
            $table->foreignId('daily_plan_id')->constrained('daily_plans')->cascadeOnDelete();
            $table->date('logged_date');
            $table->boolean('is_completed')->default(false);
            $table->boolean('used_two_minute_version')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['practice_id', 'logged_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_logs');
    }
};
