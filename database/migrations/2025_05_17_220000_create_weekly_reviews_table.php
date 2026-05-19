<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_reviews', function (Blueprint $table) {
            $table->id();
            $table->date('week_start');
            $table->date('week_end');
            $table->text('reflection_win')->nullable();
            $table->text('reflection_challenge')->nullable();
            $table->text('reflection_learning')->nullable();
            $table->text('reflection_gratitude')->nullable();
            $table->string('next_week_focus')->nullable();
            $table->json('next_week_priorities')->nullable();
            $table->tinyInteger('identity_score')->nullable();
            $table->text('identity_note')->nullable();
            $table->tinyInteger('energy_rating')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reviews');
    }
};
