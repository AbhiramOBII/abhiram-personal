<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Models\DeadlineAlert;
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

        // Recalculate stale value scores for today's tasks
        $scorer = app(\App\Services\ValueScoreService::class);
        $staleTasks = $plan->tasks()->whereIn('status', ['backlog', 'wip'])
            ->where(function ($q) {
                $q->whereNull('value_score_calculated_for')
                  ->orWhere('value_score_calculated_for', '<', today()->toDateString());
            })->get();
        foreach ($staleTasks as $staleTask) {
            $scorer->calculateAndSave($staleTask, today());
        }

        $tasks = $plan->tasks()->orderByDesc('value_score')->orderBy('sort_order')->get();

        // Also include unplanned tasks (no TBCB date)
        $floatingTasks = Task::unplanned()
            ->orderByDesc('value_score')
            ->get();

        $tasks = $tasks->merge($floatingTasks)->unique('id');
        $visibleTasks = $tasks->where('status', '!=', 'deferred');

        $groupedTasks = [];
        foreach ($timeBlocks as $block) {
            $groupedTasks[$block->id] = $visibleTasks->where('time_block_id', $block->id)->values()->toArray();
        }
        $groupedTasks['anytime'] = $visibleTasks->whereNull('time_block_id')->values()->toArray();

        $currentBlock = TimeBlock::current();
        $completed = $visibleTasks->where('status', 'done')->count();
        $total = $visibleTasks->count();
        $rolledOver = $visibleTasks->where('is_rolled_over', true)->count();
        $completionPct = $plan->completionPercentage();

        $practiceLogs = PracticeLog::where('logged_date', now()->toDateString())
            ->with('practice')
            ->get()
            ->sortBy(fn($l) => $l->practice->sort_order);

        // Load practices separated by type — filter reflective by time + incomplete
        $hour = (int) now()->format('H');
        $currentSlot = match(true) {
            $hour < 12 => 'morning',
            $hour < 17 => 'afternoon',
            default    => 'evening',
        };

        $reflectivePractices = Practice::reflective()->active()->forToday()->with(['logs' => function($q) {
            $q->where('logged_date', today());
        }])->orderBy('sort_order')->get()
            ->filter(fn ($p) => !($p->logs->first()?->response_text)) // hide answered
            ->sortBy(function ($p) use ($currentSlot) {
                // time-appropriate first, then anytime, then other slots
                $pt = $p->preferred_time ?? 'anytime';
                return $pt === $currentSlot ? 0 : ($pt === 'anytime' ? 1 : 2);
            })->values();

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
            ->where(function ($q) {
                $q->whereNull('tbcb_date')
                  ->orWhere('tbcb_date', '<=', today());
            })
            ->orderByDesc('id')
            ->limit(30)
            ->get(['id', 'title', 'pillar', 'priority', 'estimated_minutes']);

        // Project tasks — injected across all days from start to deadline
        $projectTasks = Task::activeProjects()
            ->with('deadlineAlerts')
            ->orderByRaw("CASE
                WHEN deadline_at IS NULL THEN 2
                WHEN deadline_at <= NOW() + INTERVAL 1 DAY THEN 0
                WHEN deadline_at <= NOW() + INTERVAL 3 DAY THEN 1
                ELSE 2
            END")
            ->orderBy('deadline_at')
            ->get();

        // Overdue project tasks
        $overdueProjects = Task::overdueProjects()->get();

        // Today's deadline alerts (not dismissed)
        $deadlineAlerts = DeadlineAlert::with('task')
            ->where('alert_date', today())
            ->where('is_dismissed', false)
            ->whereHas('task', fn($q) => $q->whereIn('status', ['backlog', 'wip']))
            ->orderByRaw("CASE alert_type WHEN 'overdue' THEN 0 WHEN '0d' THEN 1 WHEN '1d' THEN 2 ELSE 3 END")
            ->get();

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
            'projectTasks',
            'overdueProjects',
            'deadlineAlerts',
        ));
    }
}
