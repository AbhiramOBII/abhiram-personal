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
        Schema::table('tasks', function (Blueprint $table) {
            $table->tinyInteger('impact_rating')->default(2)->after('priority');
            $table->unsignedTinyInteger('value_score')->default(0)->after('impact_rating');
            $table->unsignedTinyInteger('theme_score')->default(0)->after('value_score');
            $table->unsignedTinyInteger('urgency_score')->default(0)->after('theme_score');
            $table->unsignedTinyInteger('efficiency_score')->default(0)->after('urgency_score');
            $table->date('value_score_calculated_for')->nullable()->after('efficiency_score');
            $table->boolean('is_resurfaced')->default(false)->after('value_score_calculated_for');
            $table->date('resurfaced_on')->nullable()->after('is_resurfaced');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'impact_rating', 'value_score', 'theme_score', 'urgency_score',
                'efficiency_score', 'value_score_calculated_for',
                'is_resurfaced', 'resurfaced_on',
            ]);
        });
    }
};
