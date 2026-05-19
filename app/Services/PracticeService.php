<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\Practice;
use App\Models\PracticeLog;

class PracticeService
{
    public function generateLogsForToday(): void
    {
        $plan = DailyPlan::today();
        $today = now()->toDateString();

        $practices = Practice::active()->forToday()->get();

        foreach ($practices as $practice) {
            PracticeLog::firstOrCreate(
                ['practice_id' => $practice->id, 'logged_date' => $today],
                ['daily_plan_id' => $plan->id, 'is_completed' => false]
            );
        }
    }

    public function completePractice(Practice $practice, bool $twoMinuteVersion = false): PracticeLog
    {
        $plan = DailyPlan::today();
        $today = now()->toDateString();

        $log = PracticeLog::firstOrCreate(
            ['practice_id' => $practice->id, 'logged_date' => $today],
            ['daily_plan_id' => $plan->id]
        );

        $log->is_completed = true;
        $log->completed_at = now();
        $log->used_two_minute_version = $twoMinuteVersion;
        $log->save();

        return $log;
    }

    public function uncompletePractice(Practice $practice): void
    {
        $log = PracticeLog::where('practice_id', $practice->id)
            ->where('logged_date', now()->toDateString())
            ->first();

        if ($log) {
            $log->is_completed = false;
            $log->completed_at = null;
            $log->used_two_minute_version = false;
            $log->save();
        }
    }

    public function getStreakData(Practice $practice): array
    {
        return [
            'current_streak' => $practice->currentStreak(),
            'longest_streak' => $practice->longestStreak(),
            'completion_rate_30' => $practice->completionRateLastDays(30),
            'total_completions' => $practice->logs()->where('is_completed', true)->count(),
        ];
    }
}
