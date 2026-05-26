<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Task;
use App\Models\WorkingDay;
use Illuminate\Http\JsonResponse;

class TodayDataController extends Controller
{
    public function index(): JsonResponse
    {
        $date = today();
        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => $date->toDateString()],
            ['working_day_id' => WorkingDay::where('day_number', $date->dayOfWeek)->first()?->id]
        );

        $scorer = app(\App\Services\ValueScoreService::class);

        // Recalculate stale scores
        $stale = $plan->tasks()->daily()
            ->whereIn('status', ['backlog', 'wip'])
            ->where(fn($q) => $q
                ->whereNull('value_score_calculated_for')
                ->orWhere('value_score_calculated_for', '<', $date))
            ->get();
        foreach ($stale as $task) {
            $scorer->calculateAndSave($task, $date);
        }

        // Planned tasks
        $planned = $plan->tasks()->daily()
            ->whereIn('status', ['backlog', 'wip'])
            ->orderByDesc('value_score')
            ->get();

        // Floating — no tbcb_date, no plan
        $floating = Task::floating()
            ->orderByDesc('value_score')
            ->get();

        // TBCB due today
        $tbcb = Task::tbcbDueToday()
            ->whereNull('daily_plan_id')
            ->orderBy('tbcb_date')
            ->orderByDesc('value_score')
            ->get();

        // Working day
        $workingDay = WorkingDay::where('day_number', now()->dayOfWeek)->first();

        return response()->json([
            'fetched_at' => now()->toIso8601String(),
            'working_day' => $workingDay ? [
                'theme' => $workingDay->theme,
                'color' => $workingDay->color ?? $workingDay->hex_color,
                'icon' => $workingDay->icon_emoji,
            ] : null,
            'planned' => $this->formatTasks($planned),
            'floating' => $this->formatTasks($floating),
            'tbcb' => $this->formatTasks($tbcb),
            'counts' => [
                'planned' => $planned->count(),
                'floating' => $floating->count(),
                'tbcb' => $tbcb->count(),
                'total' => $planned->count() + $floating->count() + $tbcb->count(),
            ],
        ]);
    }

    private function formatTasks($tasks): array
    {
        return $tasks->map(fn($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'status' => $t->status,
            'pillar' => $t->pillar,
            'priority' => $t->priority,
            'value_score' => $t->value_score,
            'impact_label' => $t->impact_label,
            'score_badge' => $t->score_badge,
            'tbcb_badge' => $t->tbcb_badge,
            'tbcb_formatted' => $t->tbcb_formatted,
            'is_resurfaced' => $t->is_resurfaced,
            'estimated_minutes' => $t->estimated_minutes,
            'is_rolled_over' => $t->is_rolled_over,
            'rollover_count' => $t->rollover_count,
            'time_block_id' => $t->time_block_id,
            'due_date' => $t->due_date?->toDateString(),
            'sort_order' => $t->sort_order,
        ])->values()->toArray();
    }
}
