<?php

namespace App\Services;

use App\Models\Task;
use App\Models\WorkingDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ValueScoreService
{
    protected array $themeMap = [
        0 => ['recovery', 'personal'],
        1 => ['revenue', 'operations'],
        2 => ['marketing'],
        3 => ['creation'],
        4 => ['networking'],
        5 => ['creation', 'marketing'],
        6 => ['networking', 'creation', 'community'],
    ];

    protected array $adjacentMap = [
        'revenue'    => ['operations', 'marketing'],
        'marketing'  => ['creation', 'revenue'],
        'creation'   => ['marketing', 'learning'],
        'networking' => ['revenue', 'community'],
        'learning'   => ['creation', 'personal'],
        'recovery'   => ['personal', 'health'],
        'operations' => ['revenue', 'admin'],
        'personal'   => ['recovery', 'health'],
    ];

    public function calculate(Task $task, ?Carbon $forDate = null): array
    {
        $forDate = $forDate ?? today();

        // --- IMPACT (0–40) ---
        $impactPoints = match ((int) $task->impact_rating) {
            4 => 40,
            3 => 30,
            2 => 20,
            1 => 10,
            0 => 0,
            default => 20,
        };

        // --- URGENCY (0–30) ---
        $urgencyPoints = 5;
        if ($task->deadline_at) {
            $daysLeft = (int) $forDate->diffInDays($task->deadline_at, false);
            $urgencyPoints = match (true) {
                $daysLeft <= 0 => 30,
                $daysLeft === 1 => 25,
                $daysLeft <= 3 => 20,
                $daysLeft <= 7 => 15,
                default => 8,
            };
        }

        $rolloverBonus = min(10, ($task->rollover_count ?? 0) * 2);
        $urgencyPoints = min(30, $urgencyPoints + $rolloverBonus);

        // --- EFFORT EFFICIENCY (0–20) ---
        $minutes = max(1, $task->estimated_minutes ?? 30);
        $rawEfficiency = ($impactPoints / $minutes) * 100;
        $efficiencyPoints = (int) min(20, round($rawEfficiency / 15));

        // --- THEME ALIGNMENT (0–10) ---
        $dayOfWeek = $forDate->dayOfWeek;
        $preferredPillars = $this->themeMap[$dayOfWeek] ?? [];
        $adjacentPillars = $this->adjacentMap[$task->pillar ?? ''] ?? [];
        $themePoints = 0;
        if (in_array($task->pillar, $preferredPillars)) {
            $themePoints = 10;
        } elseif (!empty(array_intersect($adjacentPillars, $preferredPillars))) {
            $themePoints = 5;
        }

        // --- PRIORITY BONUS ---
        $priorityBonus = match ($task->priority ?? 'should') {
            'must'  => 5,
            'bonus' => -5,
            default => 0,
        };

        // --- TOTAL ---
        $total = min(100, max(0,
            $impactPoints + $urgencyPoints + $efficiencyPoints + $themePoints + $priorityBonus
        ));

        return [
            'value_score'                => $total,
            'urgency_score'              => $urgencyPoints,
            'efficiency_score'           => $efficiencyPoints,
            'theme_score'                => $themePoints,
            'value_score_calculated_for' => $forDate->toDateString(),
        ];
    }

    public function calculateAndSave(Task $task, ?Carbon $forDate = null): Task
    {
        $scores = $this->calculate($task, $forDate);
        $task->update($scores);
        $task->refresh();
        return $task;
    }

    public function recalculateAll(?Carbon $forDate = null): int
    {
        $forDate = $forDate ?? today();

        $tasks = Task::whereIn('status', ['backlog', 'wip'])
            ->where(function ($q) use ($forDate) {
                $q->whereNull('value_score_calculated_for')
                  ->orWhere('value_score_calculated_for', '<', $forDate->toDateString());
            })
            ->get();

        foreach ($tasks as $task) {
            $this->calculateAndSave($task, $forDate);
        }

        return $tasks->count();
    }

    public function assignToSlots(Collection $tasks, Collection $timeSlots): array
    {
        $sorted = $tasks->sortByDesc('value_score')->values();

        $slotsByIntent = [
            'deep'  => $timeSlots->filter(fn($s) => in_array($s->intent ?? '', ['deep_work', 'execution']))->sortBy('start_time'),
            'light' => $timeSlots->filter(fn($s) => in_array($s->intent ?? '', ['warm_up', 'buffer', 'admin']))->sortBy('start_time'),
            'focus' => $timeSlots->filter(fn($s) => in_array($s->intent ?? '', ['focus', 'night']))->sortBy('start_time'),
        ];

        $assignment = [];
        foreach ($sorted as $task) {
            $score = $task->value_score;
            if ($score >= 70) {
                $slot = $slotsByIntent['deep']->first();
            } elseif ($score >= 40) {
                $slot = $slotsByIntent['focus']->first() ?? $slotsByIntent['deep']->first();
            } else {
                $slot = $slotsByIntent['light']->first() ?? $slotsByIntent['focus']->first();
            }

            if ($slot) {
                $assignment[$slot->id][] = $task->id;
            }
        }

        return $assignment;
    }

    public function getResurfaceCandidates(int $activeTodayCount, ?Carbon $forDate = null): Collection
    {
        $forDate = $forDate ?? today();

        if ($activeTodayCount >= 5) {
            return collect();
        }

        $needed = 5 - $activeTodayCount;

        return Task::where(function ($q) {
                $q->where('status', 'deferred')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'backlog')
                         ->where('value_score', '<', 35)
                         ->whereNull('daily_plan_id');
                  });
            })
            ->where('is_resurfaced', false)
            ->where(function ($q) use ($forDate) {
                $q->whereNull('resurfaced_on')
                  ->orWhere('resurfaced_on', '<', $forDate->toDateString());
            })
            ->orderByDesc('value_score')
            ->orderByDesc('rollover_count')
            ->limit($needed)
            ->get();
    }

    public function resurface(Task $task, $dailyPlanId, Carbon $forDate): void
    {
        $task->update([
            'status'        => 'backlog',
            'is_resurfaced' => true,
            'resurfaced_on' => $forDate->toDateString(),
            'daily_plan_id' => $dailyPlanId,
        ]);

        $this->calculateAndSave($task, $forDate);
    }
}
