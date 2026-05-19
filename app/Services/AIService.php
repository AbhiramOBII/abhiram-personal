<?php

namespace App\Services;

use App\Models\AILog;
use App\Models\AIOutput;
use App\Models\DailyPlan;
use App\Models\PracticeLog;
use App\Models\TimeBlock;
use App\Models\WeeklyReview;
use App\Models\WorkingDay;
use Carbon\Carbon;
use OpenAI\Laravel\Facades\OpenAI;

class AIService
{
    protected string $model = 'gpt-4o-mini';

    public function getDailyBriefing(DailyPlan $plan, bool $bustCache = false): string
    {
        $today = now()->toDateString();

        if (!$bustCache) {
            $cached = AIOutput::getCached('daily_briefing', $today);
            if ($cached) return $cached->content;
        }

        $workingDay = $plan->workingDay;
        $themeName = $workingDay?->theme ?? 'Focus Day';
        $energyLabel = $workingDay?->energyLabel() ?? 'Medium Energy';
        $tasks = $plan->tasks;
        $totalTasks = $tasks->count();
        $pendingTasks = $tasks->where('is_completed', false)->count();
        $rolloverCount = $tasks->where('is_rolled_over', true)->count();

        $practiceLogs = PracticeLog::where('logged_date', $today)->get();
        $completedPractices = $practiceLogs->where('is_completed', true)->count();
        $totalPractices = $practiceLogs->count();

        $upskillingService = app(UpskillingService::class);
        $learningSuggestion = $upskillingService->getTodaySuggestion();
        $learningTitle = $learningSuggestion?->title ?? 'your next learning item';

        $currentBlock = TimeBlock::current();
        $blockName = $currentBlock?->name ?? 'no active block';

        $systemPrompt = "You are a personal chief of staff for Abhiram Chandramohan, a founder and entrepreneur based in Bengaluru. He runs Obii Kriationz Web LLP, hosts a podcast called Business Giseness, and is a BNI Highflyer chapter member. Be direct, warm, and motivational. Speak in second person. Maximum 5 sentences.";

        $userPrompt = "Today is {$themeName} ({$energyLabel}). Total tasks: {$totalTasks}, pending: {$pendingTasks}, rolled over: {$rolloverCount}. Practices: {$completedPractices}/{$totalPractices} completed. Top learning item: {$learningTitle}. Current time block: {$blockName}. Current time: " . now()->format('g:i A') . ". Give a morning briefing.";

        $fallback = "Today is {$themeName}. You have {$pendingTasks} tasks ahead. Stay focused on what moves the needle. One block at a time.";

        $response = $this->callOpenAI($systemPrompt, $userPrompt, 'daily_briefing', [
            'max_tokens' => 200,
            'temperature' => 0.7,
        ]);

        if (!$response) return $fallback;

        AIOutput::updateOrCreate(
            ['feature' => 'daily_briefing', 'context_date' => $today],
            ['content' => $response, 'meta' => null]
        );

        return $response;
    }

    public function getTaskSuggestions(DailyPlan $plan, bool $bustCache = false): array
    {
        $today = now()->toDateString();

        if (!$bustCache) {
            $cached = AIOutput::getCached('task_suggestion', $today);
            if ($cached && $cached->meta) return $cached->meta;
        }

        $workingDay = $plan->workingDay;
        $themeName = $workingDay?->theme ?? 'Focus Day';
        $pendingTasks = $plan->tasks()->where('is_completed', false)->pluck('title')->toArray();
        $rolloverTasks = $plan->tasks()->where('is_rolled_over', true)->get()->map(fn($t) => "{$t->title} (rolled {$t->rollover_count}x)")->toArray();

        $timeBlocks = $workingDay?->timeBlocks()
            ->where('start_time', '>', now()->format('H:i:s'))
            ->pluck('name')->toArray() ?? [];

        $upskillingService = app(UpskillingService::class);
        $learningSuggestion = $upskillingService->getTodaySuggestion();

        $systemPrompt = 'You are a productivity assistant for a founder. Return a JSON object with a key "suggestions" containing an array of exactly 3 task suggestion objects. Each object has: title (string, max 60 chars), reason (string, max 80 chars), pillar (string), priority (must|should|bonus). Return only valid JSON, no explanation.';

        $userPrompt = "Today's theme: {$themeName}. Pending tasks: " . implode(', ', array_slice($pendingTasks, 0, 10)) . ". Rollover tasks: " . implode(', ', $rolloverTasks) . ". Remaining time blocks: " . implode(', ', $timeBlocks) . ". Upskill suggestion: " . ($learningSuggestion?->title ?? 'none') . ".";

        $response = $this->callOpenAI($systemPrompt, $userPrompt, 'task_suggestion', [
            'max_tokens' => 300,
            'temperature' => 0.5,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response) return [];

        try {
            $parsed = json_decode($response, true);
            $suggestions = $parsed['suggestions'] ?? $parsed;
            if (!is_array($suggestions)) return [];

            AIOutput::updateOrCreate(
                ['feature' => 'task_suggestion', 'context_date' => $today],
                ['content' => $response, 'meta' => $suggestions]
            );

            return $suggestions;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getOverloadGuard(DailyPlan $plan, bool $bustCache = false): ?array
    {
        $pendingTasks = $plan->tasks()->where('is_completed', false)->get();

        if ($pendingTasks->count() <= 8) return null;

        $today = now()->toDateString();

        if (!$bustCache) {
            $cached = AIOutput::getCached('overload_guard', $today);
            if ($cached && $cached->meta) return $cached->meta;
        }

        $systemPrompt = 'You are a workload advisor. Analyze the task list and identify the 2-3 tasks most worth deferring to tomorrow. Return JSON: { "overloaded": true, "message": "string max 100 chars", "defer_suggestions": ["task title 1", "task title 2"] }. Return only valid JSON.';

        $taskList = $pendingTasks->map(fn($t) => "{$t->title} (priority: {$t->priority}, est: {$t->estimated_minutes}min)")->implode('; ');

        $userPrompt = "Today's pending tasks ({$pendingTasks->count()} total): {$taskList}";

        $response = $this->callOpenAI($systemPrompt, $userPrompt, 'overload_guard', [
            'max_tokens' => 200,
            'temperature' => 0.3,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response) return null;

        try {
            $parsed = json_decode($response, true);
            if (!($parsed['overloaded'] ?? false)) return null;

            AIOutput::updateOrCreate(
                ['feature' => 'overload_guard', 'context_date' => $today],
                ['content' => $response, 'meta' => $parsed]
            );

            return $parsed;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getWeeklyInsight(WeeklyReview $review, bool $bustCache = false): string
    {
        $contextDate = $review->week_start->toDateString();

        if (!$bustCache) {
            $cached = AIOutput::getCached('weekly_insight', $contextDate);
            if ($cached) return $cached->content;
        }

        $stats = app(WeeklyReviewService::class)->getWeekStats($review);

        $systemPrompt = "You are a supportive weekly advisor for a founder. Write a 3-sentence insight about this week. "
            . "Be encouraging and understanding — some weeks are slower and that's okay. "
            . "If metrics are low or zero, acknowledge it gently without judgment, normalize rest or busy periods, and offer one small, achievable step for next week. "
            . "Never use words like 'miss', 'failure', or 'disconnect'. Tone: warm, kind, like a supportive friend.";

        $userPrompt = "Week of {$review->week_start->format('M j')}: Completion rate: {$stats['completion_rate']}%. Practice rate: {$stats['practices_rate']}%. Upskill minutes: {$stats['upskill_minutes']}. Identity score: " . ($review->identity_score ?? 'not set') . "/10. Energy rating: " . ($review->energy_rating ?? 'not set') . "/10. Pillar breakdown: " . json_encode($stats['pillar_breakdown']) . ".";

        $fallback = "This week had its highs and lows. Review what worked and double down on it next week.";

        $response = $this->callOpenAI($systemPrompt, $userPrompt, 'weekly_insight', [
            'max_tokens' => 250,
            'temperature' => 0.7,
        ]);

        if (!$response) return $fallback;

        AIOutput::updateOrCreate(
            ['feature' => 'weekly_insight', 'context_date' => $contextDate],
            ['content' => $response, 'meta' => null]
        );

        return $response;
    }

    public function getPatternInsight(bool $bustCache = false): string
    {
        if (!$bustCache) {
            $recent = AIOutput::where('feature', 'pattern_insight')
                ->where('context_date', '>=', now()->subDays(7)->toDateString())
                ->latest('context_date')
                ->first();
            if ($recent) return $recent->content;
        }

        $analyticsService = app(AnalyticsService::class);
        $from = now()->subDays(30);
        $to = now();

        $weekdays = $analyticsService->taskCompletionByWeekday($from, $to);
        $peakHours = $analyticsService->peakProductivityWindows($from, $to);
        arsort($peakHours);
        $top3Peak = array_slice(array_keys($peakHours), 0, 3);

        $practices = $analyticsService->practiceConsistency($from, $to);
        $mostConsistent = !empty($practices) ? end($practices)['name'] : 'none';
        $leastConsistent = !empty($practices) ? $practices[0]['name'] : 'none';

        $pillars = $analyticsService->taskCompletionByPillar($from, $to);
        $pillarRolled = 'none';

        $identityTrend = $analyticsService->identityScoreTrend();
        $avgIdentity = !empty($identityTrend['identity_scores']) ? round(collect($identityTrend['identity_scores'])->filter()->avg(), 1) : 'N/A';
        $avgEnergy = !empty($identityTrend['energy_ratings']) ? round(collect($identityTrend['energy_ratings'])->filter()->avg(), 1) : 'N/A';

        $systemPrompt = "You are a long-term productivity pattern analyst. Based on the data provided, identify one non-obvious insight about this person's work patterns. Be specific and actionable. Maximum 3 sentences.";

        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $weekdaySummary = collect($weekdays)->map(fn($v, $k) => $dayNames[$k] . ": {$v['rate']}%")->implode(', ');

        $userPrompt = "Last 30 days data — Task completion by weekday: {$weekdaySummary}. Top 3 peak hours: " . implode(', ', array_map(fn($h) => "{$h}:00", $top3Peak)) . ". Most consistent practice: {$mostConsistent}. Least consistent: {$leastConsistent}. Pillar breakdown: " . json_encode(array_map(fn($p) => $p['completed'], $pillars)) . ". Avg identity score: {$avgIdentity}/10. Avg energy: {$avgEnergy}/10.";

        $fallback = "Your patterns suggest room for intentional scheduling. Review your peak hours and align deep work accordingly.";

        $response = $this->callOpenAI($systemPrompt, $userPrompt, 'pattern_insight', [
            'max_tokens' => 200,
            'temperature' => 0.6,
        ]);

        if (!$response) return $fallback;

        AIOutput::updateOrCreate(
            ['feature' => 'pattern_insight', 'context_date' => now()->toDateString()],
            ['content' => $response, 'meta' => null]
        );

        return $response;
    }

    public function getDailyQuote(bool $bustCache = false): string
    {
        $today = now()->toDateString();

        if (!$bustCache) {
            $cached = AIOutput::getCached('daily_quote', $today);
            if ($cached) return $cached->content;
        }

        $dayOfWeek = now()->format('l');

        $prompt = "Give me one truly great, uplifting quote to start a {$dayOfWeek} with energy and positivity. "
            . "Pick from legendary thinkers, leaders, athletes, or creators — people like Marcus Aurelius, Maya Angelou, Steve Jobs, Kobe Bryant, Rumi, Naval Ravikant, etc. "
            . "The quote should feel like a spark — something that makes you want to go conquer the day. "
            . "Keep it under 20 words. Return ONLY the quote text followed by — and the author name. No extra text.";

        $content = $this->callOpenAI(
            'You are a curator of the world\'s most powerful positive quotes. You pick quotes that ignite action and fill the reader with optimism.',
            $prompt,
            'daily_quote',
            ['temperature' => 0.9]
        );

        if ($content) {
            $content = trim($content, "\" \n");

            AIOutput::updateOrCreate(
                ['feature' => 'daily_quote', 'context_date' => $today],
                ['content' => $content, 'meta' => null, 'is_shown' => true]
            );

            return $content;
        }

        return 'Make today count — one task at a time.';
    }

    protected function callOpenAI(string $systemPrompt, string $userPrompt, string $feature, array $options = []): ?string
    {
        $start = microtime(true);

        try {
            $params = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'max_tokens' => $options['max_tokens'] ?? 200,
                'temperature' => $options['temperature'] ?? 0.7,
            ];

            if (isset($options['response_format'])) {
                $params['response_format'] = $options['response_format'];
            }

            $result = OpenAI::chat()->create($params);

            $latency = (int) ((microtime(true) - $start) * 1000);
            $content = $result->choices[0]->message->content ?? '';

            AILog::create([
                'feature' => $feature,
                'prompt_tokens' => $result->usage->promptTokens ?? null,
                'completion_tokens' => $result->usage->completionTokens ?? null,
                'model' => $this->model,
                'prompt_summary' => mb_substr($userPrompt, 0, 100),
                'response_summary' => mb_substr($content, 0, 100),
                'latency_ms' => $latency,
                'created_at' => now(),
            ]);

            return $content;
        } catch (\Throwable $e) {
            $latency = (int) ((microtime(true) - $start) * 1000);

            AILog::create([
                'feature' => $feature,
                'prompt_tokens' => null,
                'completion_tokens' => null,
                'model' => $this->model,
                'prompt_summary' => mb_substr($userPrompt, 0, 100),
                'response_summary' => 'ERROR: ' . mb_substr($e->getMessage(), 0, 90),
                'latency_ms' => $latency,
                'created_at' => now(),
            ]);

            return null;
        }
    }
}
