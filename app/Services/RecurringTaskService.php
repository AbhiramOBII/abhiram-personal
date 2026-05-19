<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\WorkingDay;

class RecurringTaskService
{
    public function generateForToday(): void
    {
        $plan = DailyPlan::today();
        $today = WorkingDay::today();
        $dayNumber = $today?->day_number ?? now()->dayOfWeek;

        $existingTitles = $plan->tasks()->pluck('title')->toArray();

        // Recurring tasks that match today's day_number
        $recurringTasks = Task::recurring()
            ->active()
            ->whereJsonContains('recurring_days', $dayNumber)
            ->get();

        foreach ($recurringTasks as $task) {
            if (in_array($task->title, $existingTitles)) {
                continue;
            }

            Task::create([
                'daily_plan_id' => $plan->id,
                'time_block_id' => $task->time_block_id,
                'title' => $task->title,
                'pillar' => $task->pillar,
                'priority' => $task->priority,
                'estimated_minutes' => $task->estimated_minutes,
                'is_completed' => false,
                'is_rolled_over' => false,
                'parent_task_id' => null,
                'sort_order' => 0,
            ]);

            $existingTitles[] = $task->title;
        }

        // Task templates for today
        $templates = TaskTemplate::active()->forDay($dayNumber)->get();

        foreach ($templates as $template) {
            if (in_array($template->title, $existingTitles)) {
                continue;
            }

            Task::create([
                'daily_plan_id' => $plan->id,
                'time_block_id' => $template->time_block_id,
                'title' => $template->title,
                'notes' => $template->notes,
                'pillar' => $template->pillar,
                'priority' => $template->priority,
                'estimated_minutes' => $template->estimated_minutes,
                'is_completed' => false,
                'is_rolled_over' => false,
                'parent_task_id' => null,
                'sort_order' => $template->sort_order,
            ]);

            $existingTitles[] = $template->title;
        }
    }
}
