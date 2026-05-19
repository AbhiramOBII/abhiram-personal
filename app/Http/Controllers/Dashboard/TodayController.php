<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\TimeBlock;
use App\Models\PracticeLog;
use App\Models\LearningSession;
use App\Services\PracticeService;
use App\Services\RecurringTaskService;
use App\Services\RolloverService;
use App\Services\AIService;
use App\Services\NudgeService;
use App\Services\UpskillingService;

class TodayController extends Controller
{
    public function index(RecurringTaskService $recurringService, RolloverService $rolloverService, PracticeService $practiceService, UpskillingService $upskillingService, NudgeService $nudgeService, AIService $aiService)
    {
        $plan = DailyPlan::today();

        if ($plan->wasRecentlyCreated) {
            $rolloverService->rolloverYesterday();
        }

        $recurringService->generateForToday();
        $practiceService->generateLogsForToday();

        $workingDay = $plan->workingDay;
        $timeBlocks = $workingDay
            ? $workingDay->timeBlocks()->orderBy('sort_order')->orderBy('start_time')->get()
            : collect();

        $tasks = $plan->tasks()->orderBy('sort_order')->get();

        $groupedTasks = [];
        foreach ($timeBlocks as $block) {
            $groupedTasks[$block->id] = $tasks->where('time_block_id', $block->id)->values()->toArray();
        }
        $groupedTasks['anytime'] = $tasks->whereNull('time_block_id')->values()->toArray();

        $currentBlock = TimeBlock::current();
        $completed = $tasks->where('is_completed', true)->count();
        $total = $tasks->count();
        $rolledOver = $tasks->where('is_rolled_over', true)->count();
        $completionPct = $plan->completionPercentage();

        $practiceLogs = PracticeLog::where('logged_date', now()->toDateString())
            ->with('practice')
            ->get()
            ->sortBy(fn($l) => $l->practice->sort_order);

        $upskillSuggestion = $upskillingService->getTodaySuggestion();
        $todayLearningMinutes = $upskillingService->getTodayMinutes();
        $activeSession = LearningSession::where('session_date', now()->toDateString())
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        $isSundayVisionMode = now()->isSunday();

        $nudges = $nudgeService->getActiveNudges();
        $hasNudges = count($nudges) > 0;

        $aiBriefing = $aiService->getDailyBriefing($plan);
        $aiSuggestions = $aiService->getTaskSuggestions($plan);
        $overloadWarning = $aiService->getOverloadGuard($plan);
        $dailyQuote = $aiService->getDailyQuote();

        return view('dashboard.today', compact(
            'plan',
            'workingDay',
            'timeBlocks',
            'groupedTasks',
            'currentBlock',
            'completed',
            'total',
            'rolledOver',
            'completionPct',
            'practiceLogs',
            'upskillSuggestion',
            'todayLearningMinutes',
            'activeSession',
            'isSundayVisionMode',
            'nudges',
            'hasNudges',
            'aiBriefing',
            'aiSuggestions',
            'overloadWarning',
            'dailyQuote',
        ));
    }
}
