<?php

namespace App\Console\Commands;

use App\Services\ValueScoreService;
use Illuminate\Console\Command;

class RecalculateValueScores extends Command
{
    protected $signature = 'dayos:recalculate-scores';

    protected $description = 'Recalculate value scores for all active tasks';

    public function handle(ValueScoreService $service): void
    {
        $count = $service->recalculateAll(today());
        $this->info("Recalculated value scores for {$count} tasks.");
    }
}
