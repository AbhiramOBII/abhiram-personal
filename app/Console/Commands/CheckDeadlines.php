<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\DeadlineAlertService;
use Illuminate\Console\Command;

class CheckDeadlines extends Command
{
    protected $signature   = 'dayos:check-deadlines';
    protected $description = 'Check approaching deadlines and generate AI alerts';

    public function handle(DeadlineAlertService $service): void
    {
        $tasks = Task::project()
            ->whereIn('status', ['backlog', 'wip'])
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '>=', now()->subDay())
            ->where('deadline_at', '<=', now()->addDays(3)->endOfDay())
            ->get();

        foreach ($tasks as $task) {
            $service->processTask($task);
        }

        $this->info("Checked {$tasks->count()} project tasks for deadlines.");
    }
}
