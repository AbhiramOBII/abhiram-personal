<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nudges', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'login_greeting', 'block_transition', 'rollover_warning',
                'practice_reminder', 'upskill_reminder', 'weekly_review_ready',
                'streak_celebration', 'overdue_task', 'custom',
            ])->default('custom');
            $table->string('title');
            $table->text('message');
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('icon_emoji')->default('💡');
            $table->string('hex_color')->default('#4f98a3');
            $table->tinyInteger('priority')->default(5);
            $table->boolean('is_dismissible')->default(true);
            $table->tinyInteger('auto_dismiss_seconds')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nudges');
    }
};
