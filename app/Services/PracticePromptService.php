<?php

namespace App\Services;

use App\Models\AILog;
use App\Models\DailyPlan;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Models\WorkingDay;
use Carbon\Carbon;
use OpenAI\Laravel\Facades\OpenAI;

class PracticePromptService
{
    protected string $model = 'gpt-4o-mini';

    public function generatePrompt(Practice $practice, ?WorkingDay $workingDay): string
    {
        $today = now()->toDateString();

        // Check if prompt already cached in today's log
        $existingLog = PracticeLog::where('practice_id', $practice->id)
            ->where('logged_date', $today)
            ->whereNotNull('ai_prompt_used')
            ->first();

        if ($existingLog) {
            return $existingLog->ai_prompt_used;
        }

        $template = $practice->prompt_template;
        if (!$template) {
            return '';
        }

        $theme = $workingDay->theme ?? 'Focus';
        $dayName = now()->format('l');
        $pillar = $workingDay->pillars[0] ?? 'growth';

        // Fill template variables for fallback
        $filledTemplate = str_replace(
            ['{theme}', '{day}', '{pillar}'],
            [$theme, $dayName, $pillar],
            $template
        );

        $systemPrompt = "You are a reflective coach for Abhiram Chandramohan, a founder and entrepreneur in Bengaluru. He runs Obii Kriationz Web LLP, hosts Business Giseness podcast, and is a BNI Highflyer member. Generate a single focused reflective prompt. Be personal, direct, and grounded. Maximum 2 sentences. Do not use generic self-help language.";

        $userPrompt = "Practice: {$practice->name}. Template: {$filledTemplate}. Today: {$theme} day ({$dayName}). Generate the prompt now.";

        $prompt = $this->callOpenAI($systemPrompt, $userPrompt);

        if (!$prompt) {
            $prompt = $filledTemplate;
        }

        return $prompt;
    }

    public function savePromptToLog(Practice $practice, string $prompt, $date): void
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => $dateStr],
            ['working_day_id' => null]
        );

        $log = PracticeLog::firstOrCreate(
            ['practice_id' => $practice->id, 'logged_date' => $dateStr],
            ['daily_plan_id' => $plan->id, 'is_completed' => false]
        );

        $log->update(['ai_prompt_used' => $prompt]);
    }

    protected function callOpenAI(string $systemPrompt, string $userPrompt): ?string
    {
        $start = microtime(true);

        try {
            $result = OpenAI::chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'max_tokens' => 100,
                'temperature' => 0.8,
            ]);

            $latency = (int) ((microtime(true) - $start) * 1000);
            $content = $result->choices[0]->message->content ?? '';

            AILog::create([
                'feature' => 'practice_prompt',
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
                'feature' => 'practice_prompt',
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
