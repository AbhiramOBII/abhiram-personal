<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('practice_logs', function (Blueprint $table) {
            $table->text('response_text')->nullable()->after('is_completed');
            $table->text('ai_prompt_used')->nullable()->after('response_text');
            $table->unsignedInteger('quantity')->nullable()->after('ai_prompt_used');
        });
    }

    public function down(): void
    {
        Schema::table('practice_logs', function (Blueprint $table) {
            $table->dropColumn(['response_text', 'ai_prompt_used', 'quantity']);
        });
    }
};
