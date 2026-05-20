<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->enum('type', ['reflective', 'behavioral'])->default('behavioral')->after('id');
            $table->string('icon_path')->nullable()->after('icon_emoji');
            $table->string('icon_fallback_emoji')->nullable()->after('icon_path');
            $table->text('prompt_template')->nullable()->after('description');
            $table->enum('input_type', ['text_short', 'text_long', 'list'])->default('text_long')->after('prompt_template');
            $table->string('unit')->nullable()->after('input_type');
            $table->unsignedInteger('target_value')->nullable()->after('unit');
        });

        // Copy existing icon_emoji values to icon_fallback_emoji
        DB::table('practices')->whereNotNull('icon_emoji')->update([
            'icon_fallback_emoji' => DB::raw('icon_emoji'),
        ]);
    }

    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn(['type', 'icon_path', 'icon_fallback_emoji', 'prompt_template', 'input_type', 'unit', 'target_value']);
        });
    }
};
