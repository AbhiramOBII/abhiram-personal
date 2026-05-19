<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\LearningSession;
use App\Models\NudgeLog;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Models\Task;
use App\Models\TimeBlock;
use App\Models\WeeklyReview;
use App\Models\WorkingDay;
use Carbon\Carbon;

class NudgeService
{
    public function getActiveNudges(): array
    {
        $candidates = [];

        $this->checkLoginGreeting($candidates);
        $this->checkStreakCelebration($candidates);
        $this->checkWeeklyReviewReady($candidates);
        $this->checkBlockTransition($candidates);
        $this->checkRolloverWarning($candidates);
        $this->checkPracticeReminder($candidates);
        $this->checkUpskillReminder($candidates);
        $this->checkOverdueTask($candidates);

        // Sort by priority descending, return top 3
        usort($candidates, fn($a, $b) => $b['priority'] <=> $a['priority']);

        $result = array_slice($candidates, 0, 3);

        // Log each nudge as shown
        foreach ($result as $nudge) {
            NudgeLog::logShown($nudge['type'], $nudge['context_key']);
        }

        return $result;
    }

    protected function alreadyShown(string $type, string $contextKey): bool
    {
        return NudgeLog::alreadyShownToday($type, $contextKey);
    }

    protected function checkLoginGreeting(array &$candidates): void
    {
        $contextKey = 'login_greeting_' . now()->toDateString();
        if ($this->alreadyShown('login_greeting', $contextKey)) return;

        $workingDay = WorkingDay::today();
        $hour = now()->hour;
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        $plan = DailyPlan::today();
        $taskCount = $plan->tasks()->where('is_completed', false)->count();
        $practiceCount = PracticeLog::where('logged_date', now()->toDateString())
            ->where('is_completed', false)->count();

        $themeName = $workingDay?->theme ?? 'your day';
        $energyLabel = $workingDay?->energyLabel() ?? '';

        $candidates[] = [
            'type' => 'login_greeting',
            'title' => "{$greeting} Abhiram 👋",
            'message' => ucfirst($themeName) . ($energyLabel ? " • {$energyLabel}" : '') . ". You have {$taskCount} tasks and {$practiceCount} practices waiting.",
            'cta_label' => null,
            'cta_url' => null,
            'icon_emoji' => $workingDay?->icon_emoji ?? '☀️',
            'hex_color' => $workingDay?->hex_color ?? '#4f98a3',
            'priority' => 6,
            'is_dismissible' => true,
            'auto_dismiss_seconds' => 6,
            'context_key' => $contextKey,
        ];
    }

    protected function checkStreakCelebration(array &$candidates): void
    {
        $milestones = [30, 21, 14, 7, 3];
        $practices = Practice::active()->get();

        foreach ($milestones as $milestone) {
            foreach ($practices as $practice) {
                $streak = $practice->currentStreak();
                if ($streak === $milestone) {
                    $contextKey = "streak_{$practice->id}_{$milestone}";
                    if ($this->alreadyShown('streak_celebration', $contextKey)) continue;

                    $candidates[] = [
                        'type' => 'streak_celebration',
                        'title' => "🔥 {$milestone}-Day Streak!",
                        'message' => "You've completed {$practice->name} for {$milestone} days in a row. That's identity-level consistency.",
                        'cta_label' => null,
                        'cta_url' => null,
                        'icon_emoji' => '🔥',
                        'hex_color' => $practice->hex_color ?? '#f59e0b',
                        'priority' => 9,
                        'is_dismissible' => true,
                        'auto_dismiss_seconds' => 8,
                        'context_key' => $contextKey,
                    ];
                    return; // Only fire for the highest milestone
                }
            }
        }
    }

    protected function checkWeeklyReviewReady(array &$candidates): void
    {
        if (!now()->isSunday()) return;

        $review = WeeklyReview::currentWeek();
        if ($review->is_completed) return;

        $contextKey = 'weekly_review_' . $review->week_start->toDateString();
        if ($this->alreadyShown('weekly_review_ready', $contextKey)) return;

        $candidates[] = [
            'type' => 'weekly_review_ready',
            'title' => '📋 Your Weekly Review is Ready',
            'message' => "Take 10 minutes to close this week with intention. Vision Day is your most powerful hour.",
            'cta_label' => 'Open Review →',
            'cta_url' => '/admin/weekly-review',
            'icon_emoji' => '📋',
            'hex_color' => '#4f98a3',
            'priority' => 8,
            'is_dismissible' => true,
            'auto_dismiss_seconds' => null,
            'context_key' => $contextKey,
        ];
    }

    protected function checkBlockTransition(array &$candidates): void
    {
        $workingDay = WorkingDay::today();
        if (!$workingDay) return;

        $now = now();
        $blocks = $workingDay->timeBlocks()->get();

        foreach ($blocks as $block) {
            if (!$block->start_time) continue;

            $blockStart = Carbon::parse($now->toDateString() . ' ' . $block->start_time);
            $diffMinutes = $now->diffInMinutes($blockStart, false);

            // Within 5 minutes before or after block start
            if ($diffMinutes >= -5 && $diffMinutes <= 5) {
                $contextKey = "block_transition_{$block->id}_" . $now->toDateString();
                if ($this->alreadyShown('block_transition', $contextKey)) continue;

                $typeEmojis = ['work' => '⚡', 'break' => '☕', 'free' => '🌊', 'recovery' => '🌿'];
                $emoji = $typeEmojis[$block->type] ?? '⚡';

                $candidates[] = [
                    'type' => 'block_transition',
                    'title' => "{$emoji} {$block->name} Starting",
                    'message' => $block->intent ?? "Time to transition to {$block->name}.",
                    'cta_label' => 'View Tasks →',
                    'cta_url' => '/admin/today',
                    'icon_emoji' => $emoji,
                    'hex_color' => $workingDay->hex_color ?? '#4f98a3',
                    'priority' => 7,
                    'is_dismissible' => true,
                    'auto_dismiss_seconds' => 10,
                    'context_key' => $contextKey,
                ];
                return; // Only one block transition at a time
            }
        }
    }

    protected function checkRolloverWarning(array &$candidates): void
    {
        $plan = DailyPlan::today();
        $worstOffender = $plan->tasks()
            ->where('rollover_count', '>=', 3)
            ->where('is_completed', false)
            ->orderByDesc('rollover_count')
            ->first();

        if (!$worstOffender) return;

        $contextKey = "rollover_{$worstOffender->id}_" . now()->toDateString();
        if ($this->alreadyShown('rollover_warning', $contextKey)) return;

        $candidates[] = [
            'type' => 'rollover_warning',
            'title' => '⚠️ Chronic Rollover Detected',
            'message' => "\"{$worstOffender->title}\" has rolled over {$worstOffender->rollover_count} times. Time to reschedule, break it down, or let it go.",
            'cta_label' => 'Review Tasks →',
            'cta_url' => '/admin/tasks',
            'icon_emoji' => '⚠️',
            'hex_color' => '#f59e0b',
            'priority' => 7,
            'is_dismissible' => true,
            'auto_dismiss_seconds' => null,
            'context_key' => $contextKey,
        ];
    }

    protected function checkPracticeReminder(array &$candidates): void
    {
        if (now()->hour < 21) return;

        $contextKey = 'practice_evening_reminder_' . now()->toDateString();
        if ($this->alreadyShown('practice_reminder', $contextKey)) return;

        $incomplete = PracticeLog::where('logged_date', now()->toDateString())
            ->where('is_completed', false)->count();

        if ($incomplete === 0) return;

        $candidates[] = [
            'type' => 'practice_reminder',
            'title' => '🌙 Practices Before Bed',
            'message' => "You have {$incomplete} practices left today. Even the 2-minute version counts.",
            'cta_label' => 'View Practices →',
            'cta_url' => '/admin/today#practices',
            'icon_emoji' => '🌙',
            'hex_color' => '#6366f1',
            'priority' => 5,
            'is_dismissible' => true,
            'auto_dismiss_seconds' => 8,
            'context_key' => $contextKey,
        ];
    }

    protected function checkUpskillReminder(array &$candidates): void
    {
        $hour = now()->hour;
        $minute = now()->minute;
        $timeInMinutes = $hour * 60 + $minute;

        $session = null;
        if ($timeInMinutes >= 420 && $timeInMinutes <= 630) { // 7:00 – 10:30 AM
            $session = 'morning';
        } elseif ($timeInMinutes >= 1080 && $timeInMinutes <= 1260) { // 6:00 – 9:00 PM
            $session = 'evening';
        }

        if (!$session) return;

        $contextKey = "upskill_reminder_{$session}_" . now()->toDateString();
        if ($this->alreadyShown('upskill_reminder', $contextKey)) return;

        $hasSession = LearningSession::where('session_date', now()->toDateString())->exists();
        if ($hasSession) return;

        $upskillingService = app(UpskillingService::class);
        $suggestion = $upskillingService->getTodaySuggestion();
        $itemTitle = $suggestion?->title ?? 'your next learning item';

        $candidates[] = [
            'type' => 'upskill_reminder',
            'title' => '🧠 Upskill Window Open',
            'message' => "This is a good time for {$itemTitle}. Even 20 minutes compounds.",
            'cta_label' => 'Start Learning →',
            'cta_url' => '/admin/upskilling',
            'icon_emoji' => '🧠',
            'hex_color' => '#8b5cf6',
            'priority' => 4,
            'is_dismissible' => true,
            'auto_dismiss_seconds' => 8,
            'context_key' => $contextKey,
        ];
    }

    protected function checkOverdueTask(array &$candidates): void
    {
        $plan = DailyPlan::today();
        $overdueTask = $plan->tasks()
            ->where('is_completed', false)
            ->whereDate('due_date', now()->toDateString())
            ->orderByDesc('priority')
            ->first();

        if (!$overdueTask) return;

        $contextKey = "overdue_{$overdueTask->id}_" . now()->toDateString();
        if ($this->alreadyShown('overdue_task', $contextKey)) return;

        $candidates[] = [
            'type' => 'overdue_task',
            'title' => '📅 Due Today',
            'message' => "\"{$overdueTask->title}\" is due today.",
            'cta_label' => 'View Task →',
            'cta_url' => '/admin/today',
            'icon_emoji' => '📅',
            'hex_color' => '#ef4444',
            'priority' => 6,
            'is_dismissible' => true,
            'auto_dismiss_seconds' => null,
            'context_key' => $contextKey,
        ];
    }
}
