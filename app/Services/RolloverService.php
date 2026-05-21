<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\Task;

class RolloverService
{
    public function rolloverYesterday(): void
    {
        $yesterday = now()->subDay()->toDateString();
        $yesterdayPlan = DailyPlan::where('plan_date', $yesterday)->first();

        if (!$yesterdayPlan) {
            return;
        }

        $todayPlan = DailyPlan::today();

        // Only roll over daily tasks — project tasks appear via activeProjects() scope
        $incompleteTasks = $yesterdayPlan->tasks()
            ->whereIn('status', ['backlog', 'wip'])
            ->whereNull('archived_at')
            ->whereNull('parent_task_id')
            ->where(function ($q) {
                $q->where('task_type', 'daily')->orWhereNull('task_type');
            })
            ->get();

        foreach ($incompleteTasks as $task) {
            $newCount = $task->rollover_count + 1;

            Task::create([
                'daily_plan_id' => $todayPlan->id,
                'time_block_id' => null,
                'title' => $task->title,
                'notes' => $task->notes,
                'pillar' => $task->pillar,
                'priority' => $newCount >= 3 ? 'must' : $task->priority,
                'estimated_minutes' => $task->estimated_minutes,
                'status' => 'backlog',
                'is_completed' => false,
                'completed_at' => null,
                'is_rolled_over' => true,
                'rolled_from_date' => $yesterday,
                'rollover_count' => $newCount,
                'sort_order' => 0,
                'parent_task_id' => null,
            ]);

            $task->archive();
        }
    }
}
