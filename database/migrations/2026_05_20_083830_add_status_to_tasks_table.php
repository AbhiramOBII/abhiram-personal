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
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['backlog', 'wip', 'done', 'deferred'])
                  ->default('backlog')
                  ->after('is_completed');
        });

        // Migrate existing data
        DB::statement("UPDATE tasks SET status = 'done' WHERE is_completed = 1");
        DB::statement("UPDATE tasks SET status = 'backlog' WHERE is_completed = 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
