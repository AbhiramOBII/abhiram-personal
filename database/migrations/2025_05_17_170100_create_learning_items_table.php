<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_domain_id')->constrained('skill_domains')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['course', 'book', 'video', 'article', 'podcast', 'experiment'])->default('course');
            $table->string('source_url')->nullable();
            $table->decimal('estimated_hours', 5, 1)->nullable();
            $table->boolean('is_completed')->default(false);
            $table->date('completed_at')->nullable();
            $table->tinyInteger('priority')->default(5);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_items');
    }
};
