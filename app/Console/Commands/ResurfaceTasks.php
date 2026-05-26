<?php

namespace App\Console\Commands;

use App\Models\DailyPlan;
use App\Models\Task;
use App\Services\ValueScoreService;
use Illuminate\Console\Command;

class ResurfaceTasks extends Command
{
    protected $signature = 'dayos:resurface-tasks';

    protected $description = 'Resurface deferred and low-value tasks when today is light';

    public function handle(ValueScoreService $service): void
    {
        $today = today();
        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => $today->toDateString()],
            ['working_day_id' => \App\Models\WorkingDay::today()?->id]
        );

        $activeCount = Task::whereIn('status', ['backlog', 'wip'])
            ->where('daily_plan_id', $plan->id)
            ->count();

        $candidates = $service->getResurfaceCandidates($activeCount, $today);

        foreach ($candidates as $task) {
            $service->resurface($task, $plan->id, $today);
        }

        $this->info("Resurfaced {$candidates->count()} tasks for today.");
    }
}
