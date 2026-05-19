<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_item_id')->nullable()->constrained('learning_items')->nullOnDelete();
            $table->foreignId('skill_domain_id')->nullable()->constrained('skill_domains')->nullOnDelete();
            $table->foreignId('daily_plan_id')->nullable()->constrained('daily_plans')->nullOnDelete();
            $table->date('session_date');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->smallInteger('duration_minutes')->nullable();
            $table->text('takeaway')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_sessions');
    }
};
