<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\LearningSession;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Models\Task;
use App\Models\WeeklyReview;
use Carbon\Carbon;

class WeeklyReviewService
{
    public function getWeekStats(WeeklyReview $review): array
    {
        $start = $review->week_start->toDateString();
        $end = $review->week_end->toDateString();

        $planIds = DailyPlan::whereBetween('plan_date', [$start, $end])->pluck('id');

        $tasksPlanned = Task::whereIn('daily_plan_id', $planIds)->count();
        $tasksCompleted = Task::whereIn('daily_plan_id', $planIds)->where('is_completed', true)->count();
        $tasksRolledOver = Task::whereIn('daily_plan_id', $planIds)->where('is_rolled_over', true)->count();
        $completionRate = $tasksPlanned > 0 ? (int) round(($tasksCompleted / $tasksPlanned) * 100) : 0;

        $practicesPossible = PracticeLog::whereBetween('logged_date', [$start, $end])->count();
        $practicesCompleted = PracticeLog::whereBetween('logged_date', [$start, $end])->where('is_completed', true)->count();
        $practicesRate = $practicesPossible > 0 ? (int) round(($practicesCompleted / $practicesPossible) * 100) : 0;

        $upskillMinutes = (int) LearningSession::whereBetween('session_date', [$start, $end])
            ->whereNotNull('duration_minutes')
            ->sum('duration_minutes');
        $upskillSessions = LearningSession::whereBetween('session_date', [$start, $end])->count();

        $pillarBreakdown = Task::whereIn('daily_plan_id', $planIds)
            ->where('is_completed', true)
            ->whereNotNull('pillar')
            ->selectRaw('pillar, count(*) as cnt')
            ->groupBy('pillar')
            ->pluck('cnt', 'pillar')
            ->toArray();

        $dayCompletionRates = [];
        $plans = DailyPlan::whereBetween('plan_date', [$start, $end])->get();
        foreach ($plans as $plan) {
            $dayCompletionRates[$plan->plan_date->toDateString()] = $plan->completionPercentage();
        }

        $bestDay = !empty($dayCompletionRates) ? array_keys($dayCompletionRates, max($dayCompletionRates))[0] : null;
        $worstDay = !empty($dayCompletionRates) ? array_keys($dayCompletionRates, min($dayCompletionRates))[0] : null;

        $topPracticeStreak = Practice::active()->get()
            ->sortByDesc(fn($p) => $p->currentStreak())
            ->first();

        $rolloverOffenders = Task::whereIn('daily_plan_id', $planIds)
            ->where('rollover_count', '>=', 2)
            ->select('id', 'title', 'rollover_count')
            ->orderByDesc('rollover_count')
            ->get()
            ->map(fn($t) => ['id' => $t->id, 'title' => $t->title, 'rollover_count' => $t->rollover_count])
            ->toArray();

        return [
            'tasks_planned' => $tasksPlanned,
            'tasks_completed' => $tasksCompleted,
            'tasks_rolled_over' => $tasksRolledOver,
            'completion_rate' => $completionRate,
            'practices_possible' => $practicesPossible,
            'practices_completed' => $practicesCompleted,
            'practices_rate' => $practicesRate,
            'upskill_minutes' => $upskillMinutes,
            'upskill_sessions' => $upskillSessions,
            'pillar_breakdown' => $pillarBreakdown,
            'day_completion_rates' => $dayCompletionRates,
            'best_day' => $bestDay,
            'worst_day' => $worstDay,
            'top_practice_streak' => $topPracticeStreak,
            'rollover_offenders' => $rolloverOffenders,
        ];
    }

    public function getIdentityPrompt(WeeklyReview $review): string
    {
        $stats = $this->getWeekStats($review);
        $pillars = $stats['pillar_breakdown'];

        if (empty($pillars)) {
            return 'Who did you show up as most this week — the operator, the creator, or the visionary?';
        }

        $topPillar = array_keys($pillars, max($pillars))[0];

        return match (true) {
            in_array($topPillar, ['revenue', 'operations']) => 'You invested heavily in revenue & operations this week. Did you show up as the founder you are becoming?',
            in_array($topPillar, ['learning', 'growth']) => 'You prioritized growth this week. How close are you to the person you are building?',
            in_array($topPillar, ['content', 'creation', 'media']) => 'You leaned into creation this week. Are you becoming the voice you want to be known for?',
            default => 'Who did you show up as most this week — the operator, the creator, or the visionary?',
        };
    }

    public function generateNextWeekSuggestions(WeeklyReview $review): array
    {
        $stats = $this->getWeekStats($review);
        $suggestions = [];

        // Suggest the lowest pillar
        $pillars = $stats['pillar_breakdown'];
        $allPillars = ['revenue', 'marketing', 'creation', 'networking', 'learning', 'recovery'];
        $lowestPillar = null;
        $lowestCount = PHP_INT_MAX;

        foreach ($allPillars as $p) {
            $count = $pillars[$p] ?? 0;
            if ($count < $lowestCount) {
                $lowestCount = $count;
                $lowestPillar = $p;
            }
        }

        if ($lowestPillar) {
            $suggestions[] = "Dedicate more time to " . ucfirst($lowestPillar) . " — it was underserved this week";
        }

        // Suggest based on rollover offenders
        if (!empty($stats['rollover_offenders'])) {
            $topRollover = $stats['rollover_offenders'][0];
            $suggestions[] = "Finally tackle \"{$topRollover['title']}\" — it has been deferred {$topRollover['rollover_count']} times";
        } else {
            $suggestions[] = "Keep the momentum — no chronic rollovers this week";
        }

        // Suggest based on upskilling
        $upskillDomain = \App\Models\SkillDomain::active()->inRandomOrder()->first();
        if ($upskillDomain) {
            $suggestions[] = "Continue upskilling in {$upskillDomain->name}";
        } else {
            $suggestions[] = "Set aside time for focused learning";
        }

        return array_slice($suggestions, 0, 3);
    }
}
