<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('parent_task_id')->nullable()->after('sort_order')->constrained('tasks')->nullOnDelete();
            $table->boolean('is_recurring')->default(false)->after('parent_task_id');
            $table->json('recurring_days')->nullable()->after('is_recurring');
            $table->enum('recurring_type', ['daily', 'weekly', 'theme_day'])->nullable()->after('recurring_days');
            $table->date('due_date')->nullable()->after('recurring_type');
            $table->timestamp('archived_at')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
            $table->dropColumn(['parent_task_id', 'is_recurring', 'recurring_days', 'recurring_type', 'due_date', 'archived_at']);
        });
    }
};
