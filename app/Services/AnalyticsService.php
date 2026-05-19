<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\LearningSession;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Models\SkillDomain;
use App\Models\Task;
use App\Models\WeeklyReview;
use App\Models\WorkingDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function taskCompletionByWeekday(Carbon $from, Carbon $to): array
    {
        $plans = DailyPlan::whereBetween('plan_date', [$from->toDateString(), $to->toDateString()])->get();
        $result = [];

        for ($d = 0; $d <= 6; $d++) {
            $result[$d] = ['planned' => 0, 'completed' => 0, 'rate' => 0];
        }

        foreach ($plans as $plan) {
            $dayNum = $plan->plan_date->dayOfWeek;
            $planned = $plan->tasks()->count();
            $completed = $plan->tasks()->where('is_completed', true)->count();
            $result[$dayNum]['planned'] += $planned;
            $result[$dayNum]['completed'] += $completed;
        }

        foreach ($result as $d => &$data) {
            $data['rate'] = $data['planned'] > 0 ? (int) round(($data['completed'] / $data['planned']) * 100) : 0;
        }

        return $result;
    }

    public function taskCompletionByPillar(Carbon $from, Carbon $to): array
    {
        $planIds = DailyPlan::whereBetween('plan_date', [$from->toDateString(), $to->toDateString()])->pluck('id');

        $tasks = Task::whereIn('daily_plan_id', $planIds)
            ->selectRaw("COALESCE(pillar, 'untagged') as pillar_key, count(*) as total, sum(is_completed) as completed")
            ->groupBy('pillar_key')
            ->get();

        $result = [];
        foreach ($tasks as $t) {
            $result[$t->pillar_key] = [
                'total' => (int) $t->total,
                'completed' => (int) $t->completed,
                'rate' => $t->total > 0 ? (int) round(($t->completed / $t->total) * 100) : 0,
            ];
        }

        return $result;
    }

    public function dailyCompletionHeatmap(Carbon $from, Carbon $to): array
    {
        $plans = DailyPlan::whereBetween('plan_date', [$from->toDateString(), $to->toDateString()])->get();
        $result = [];

        foreach ($plans as $plan) {
            $result[$plan->plan_date->toDateString()] = $plan->completionPercentage();
        }

        return $result;
    }

    public function peakProductivityWindows(Carbon $from, Carbon $to): array
    {
        $planIds = DailyPlan::whereBetween('plan_date', [$from->toDateString(), $to->toDateString()])->pluck('id');

        $hours = Task::whereIn('daily_plan_id', $planIds)
            ->where('is_completed', true)
            ->whereNotNull('completed_at')
            ->selectRaw('HOUR(completed_at) as hr, count(*) as cnt')
            ->groupBy('hr')
            ->orderBy('hr')
            ->pluck('cnt', 'hr')
            ->toArray();

        $full = [];
        for ($h = 0; $h < 24; $h++) {
            $full[$h] = $hours[$h] ?? 0;
        }

        return $full;
    }

    public function rolloverTrend(Carbon $from, Carbon $to): array
    {
        $result = [];
        $cursor = $from->copy()->startOfWeek(Carbon::MONDAY);

        while ($cursor->lte($to)) {
            $weekEnd = $cursor->copy()->addDays(6);
            $planIds = DailyPlan::whereBetween('plan_date', [$cursor->toDateString(), $weekEnd->toDateString()])->pluck('id');

            $result[$cursor->toDateString()] = [
                'rolled_over_count' => Task::whereIn('daily_plan_id', $planIds)->where('is_rolled_over', true)->count(),
                'completed_count' => Task::whereIn('daily_plan_id', $planIds)->where('is_completed', true)->count(),
            ];

            $cursor->addWeek();
        }

        return $result;
    }

    public function practiceConsistency(Carbon $from, Carbon $to): array
    {
        $practices = Practice::active()->get();
        $result = [];

        foreach ($practices as $practice) {
            $possible = PracticeLog::where('practice_id', $practice->id)
                ->whereBetween('logged_date', [$from->toDateString(), $to->toDateString()])
                ->count();

            $completed = PracticeLog::where('practice_id', $practice->id)
                ->whereBetween('logged_date', [$from->toDateString(), $to->toDateString()])
                ->where('is_completed', true)
                ->count();

            $result[] = [
                'id' => $practice->id,
                'name' => $practice->name,
                'emoji' => $practice->icon_emoji,
                'hex_color' => $practice->hex_color,
                'completed_days' => $completed,
                'possible_days' => $possible,
                'rate' => $possible > 0 ? (int) round(($completed / $possible) * 100) : 0,
                'current_streak' => $practice->currentStreak(),
                'longest_streak' => $practice->longestStreak(),
            ];
        }

        usort($result, fn($a, $b) => $a['rate'] <=> $b['rate']);

        return $result;
    }

    public function upskillingTrend(Carbon $from, Carbon $to): array
    {
        $result = ['weekly' => [], 'domains' => []];
        $cursor = $from->copy()->startOfWeek(Carbon::MONDAY);

        while ($cursor->lte($to)) {
            $weekEnd = $cursor->copy()->addDays(6);

            $sessions = LearningSession::whereBetween('session_date', [$cursor->toDateString(), $weekEnd->toDateString()]);
            $result['weekly'][$cursor->toDateString()] = [
                'total_minutes' => (int) $sessions->sum('duration_minutes'),
                'session_count' => $sessions->count(),
            ];

            $cursor->addWeek();
        }

        $domainBreakdown = LearningSession::whereBetween('session_date', [$from->toDateString(), $to->toDateString()])
            ->whereNotNull('skill_domain_id')
            ->selectRaw('skill_domain_id, sum(duration_minutes) as total_min, count(*) as cnt')
            ->groupBy('skill_domain_id')
            ->get();

        foreach ($domainBreakdown as $row) {
            $domain = SkillDomain::find($row->skill_domain_id);
            if ($domain) {
                $result['domains'][] = [
                    'name' => $domain->name,
                    'emoji' => $domain->icon_emoji,
                    'hex_color' => $domain->hex_color,
                    'total_minutes' => (int) $row->total_min,
                    'session_count' => (int) $row->cnt,
                ];
            }
        }

        return $result;
    }

    public function weeklyReviewTrend(): array
    {
        return WeeklyReview::where('is_completed', true)
            ->orderBy('week_start')
            ->get()
            ->map(function ($review) {
                $service = app(WeeklyReviewService::class);
                $stats = $service->getWeekStats($review);
                return [
                    'week_start' => $review->week_start->toDateString(),
                    'completion_rate' => $stats['completion_rate'],
                    'identity_score' => $review->identity_score,
                    'energy_rating' => $review->energy_rating,
                    'upskill_minutes' => $stats['upskill_minutes'],
                ];
            })
            ->toArray();
    }

    public function identityScoreTrend(): array
    {
        $reviews = WeeklyReview::where('is_completed', true)
            ->orderBy('week_start')
            ->get();

        return [
            'labels' => $reviews->pluck('week_start')->map(fn($d) => $d->format('M j'))->toArray(),
            'identity_scores' => $reviews->pluck('identity_score')->toArray(),
            'energy_ratings' => $reviews->pluck('energy_rating')->toArray(),
        ];
    }

    public function monthlySnapshot(Carbon $month): array
    {
        $from = $month->copy()->startOfMonth();
        $to = $month->copy()->endOfMonth();

        $planIds = DailyPlan::whereBetween('plan_date', [$from->toDateString(), $to->toDateString()])->pluck('id');

        $tasksPlanned = Task::whereIn('daily_plan_id', $planIds)->count();
        $tasksCompleted = Task::whereIn('daily_plan_id', $planIds)->where('is_completed', true)->count();

        $practicesPossible = PracticeLog::whereBetween('logged_date', [$from->toDateString(), $to->toDateString()])->count();
        $practicesCompleted = PracticeLog::whereBetween('logged_date', [$from->toDateString(), $to->toDateString()])->where('is_completed', true)->count();
        $practicesRate = $practicesPossible > 0 ? (int) round(($practicesCompleted / $practicesPossible) * 100) : 0;

        $upskillHours = round(
            LearningSession::whereBetween('session_date', [$from->toDateString(), $to->toDateString()])->sum('duration_minutes') / 60,
            1
        );

        $reviews = WeeklyReview::where('is_completed', true)
            ->whereBetween('week_start', [$from->toDateString(), $to->toDateString()])
            ->get();

        $avgIdentity = $reviews->whereNotNull('identity_score')->avg('identity_score');
        $avgEnergy = $reviews->whereNotNull('energy_rating')->avg('energy_rating');

        $bestWeek = $reviews->sortByDesc(function ($r) {
            return app(WeeklyReviewService::class)->getWeekStats($r)['completion_rate'];
        })->first();

        return [
            'month' => $from->format('F Y'),
            'tasks_planned' => $tasksPlanned,
            'tasks_completed' => $tasksCompleted,
            'completion_rate' => $tasksPlanned > 0 ? (int) round(($tasksCompleted / $tasksPlanned) * 100) : 0,
            'practices_rate' => $practicesRate,
            'upskill_hours' => $upskillHours,
            'avg_identity_score' => $avgIdentity ? round($avgIdentity, 1) : null,
            'avg_energy_rating' => $avgEnergy ? round($avgEnergy, 1) : null,
            'best_week' => $bestWeek?->week_start?->format('M j'),
            'reviews' => $reviews,
        ];
    }
}
