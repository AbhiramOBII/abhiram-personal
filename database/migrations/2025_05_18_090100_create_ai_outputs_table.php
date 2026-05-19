<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_outputs', function (Blueprint $table) {
            $table->id();
            $table->string('feature');
            $table->date('context_date');
            $table->text('content');
            $table->json('meta')->nullable();
            $table->boolean('is_shown')->default(false);
            $table->timestamps();

            $table->unique(['feature', 'context_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_outputs');
    }
};
