<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE ai_logs MODIFY COLUMN feature ENUM('daily_briefing','task_suggestion','overload_guard','weekly_insight','pattern_insight','daily_quote','custom','practice_prompt')");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE ai_logs MODIFY COLUMN feature ENUM('daily_briefing','task_suggestion','overload_guard','weekly_insight','pattern_insight','daily_quote','custom')");
    }
};
