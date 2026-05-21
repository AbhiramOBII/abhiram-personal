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
            $table->enum('task_type', ['daily', 'project'])->default('daily')->after('status');
            $table->date('start_date')->nullable()->after('task_type');
            $table->dateTime('deadline_at')->nullable()->after('start_date');
            $table->text('deadline_notes')->nullable()->after('deadline_at');
            $table->boolean('deadline_notified_3d')->default(false)->after('deadline_notes');
            $table->boolean('deadline_notified_1d')->default(false)->after('deadline_notified_3d');
            $table->boolean('deadline_notified_0d')->default(false)->after('deadline_notified_1d');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'task_type', 'start_date', 'deadline_at', 'deadline_notes',
                'deadline_notified_3d', 'deadline_notified_1d', 'deadline_notified_0d',
            ]);
        });
    }
};
