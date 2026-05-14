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
            $table->tinyInteger('day_number')->unique(); // 0=Sunday, 1=Monday...6=Saturday
            $table->string('day_name');
            $table->string('theme');
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // hex color for UI
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_days');
    }
};
