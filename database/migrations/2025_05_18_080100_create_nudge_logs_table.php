<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nudge_logs', function (Blueprint $table) {
            $table->id();
            $table->string('nudge_type');
            $table->string('context_key')->nullable();
            $table->date('shown_date');
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();

            $table->unique(['nudge_type', 'context_key', 'shown_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nudge_logs');
    }
};
