<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_plans', function (Blueprint $table) {
            $table->id();
            $table->date('plan_date')->unique();
            $table->foreignId('working_day_id')->nullable()->constrained('working_days')->nullOnDelete();
            $table->string('focus_intention')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_reviewed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_plans');
    }
};
