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
            $table->enum('preferred_time', ['morning', 'afternoon', 'evening', 'anytime'])
                  ->default('anytime')
                  ->after('frequency_days');
        });

        // Auto-assign based on practice names
        DB::table('practices')
            ->where('name', 'like', '%Morning%')
            ->orWhere('name', 'like', '%BNI Prep%')
            ->update(['preferred_time' => 'morning']);

        DB::table('practices')
            ->where('name', 'like', '%Evening%')
            ->orWhere('name', 'like', '%Gratitude%')
            ->update(['preferred_time' => 'evening']);
    }

    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn('preferred_time');
        });
    }
};
