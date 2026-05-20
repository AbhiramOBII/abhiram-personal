<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Models\Task;
use App\Models\TimeBlock;
use App\Models\LearningSession;
use App\Models\WorkingDay;
use App\Services\PracticeService;
use App\Services\PracticePromptService;
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

        $rolloverService->rolloverYesterday();
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
        $completed = $tasks->where('status', 'done')->count();
        $total = $tasks->count();
        $rolledOver = $tasks->where('is_rolled_over', true)->count();
        $completionPct = $plan->completionPercentage();

        $practiceLogs = PracticeLog::where('logged_date', now()->toDateString())
            ->with('practice')
            ->get()
            ->sortBy(fn($l) => $l->practice->sort_order);

        // Load practices separated by type
        $reflectivePractices = Practice::reflective()->active()->forToday()->with(['logs' => function($q) {
            $q->where('logged_date', today());
        }])->orderBy('sort_order')->get();

        $behavioralPractices = Practice::behavioral()->active()->forToday()->with(['logs' => function($q) {
            $q->where('logged_date', today());
        }])->orderBy('sort_order')->get();

        // Generate AI prompts for reflective practices
        $practicePromptService = app(PracticePromptService::class);
        foreach ($reflectivePractices as $practice) {
            $log = $practice->logs->first() ?? new PracticeLog(['practice_id' => $practice->id, 'logged_date' => today()]);
            if (empty($log->ai_prompt_used) && $practice->prompt_template) {
                $prompt = $practicePromptService->generatePrompt($practice, $workingDay);
                $practicePromptService->savePromptToLog($practice, $prompt, today());
                $log->ai_prompt_used = $prompt;
            }
            $practice->setRelation('todayLog', collect([$log]));
        }

        foreach ($behavioralPractices as $practice) {
            $log = $practice->logs->first() ?? new PracticeLog(['practice_id' => $practice->id, 'logged_date' => today(), 'is_completed' => false]);
            $practice->setRelation('todayLog', collect([$log]));
        }

        $upskillSuggestion = $upskillingService->getTodaySuggestion();
        $todayLearningMinutes = $upskillingService->getTodayMinutes();
        $activeSession = LearningSession::where('session_date', now()->toDateString())
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        $isSundayVisionMode = now()->isSunday();

        $pendingTasks = Task::active()
            ->whereNull('parent_task_id')
            ->whereIn('status', ['backlog', 'wip'])
            ->where('daily_plan_id', '!=', $plan->id)
            ->orderByDesc('id')
            ->limit(30)
            ->get(['id', 'title', 'pillar', 'priority', 'estimated_minutes']);

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
            'reflectivePractices',
            'behavioralPractices',
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
            'pendingTasks',
        ));
    }
}
