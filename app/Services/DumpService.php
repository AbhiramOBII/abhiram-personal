<?php

namespace App\Services;

use App\Models\AILog;
use OpenAI\Laravel\Facades\OpenAI;

class DumpService
{
    public function categorise(array $lines): array
    {
        $filtered = array_values(array_filter($lines, fn($l) => mb_strlen(trim($l)) >= 3));

        if (empty($filtered)) {
            return [];
        }

        $systemPrompt = <<<'PROMPT'
You are a task categorisation assistant for Abhiram Chandramohan, a founder and entrepreneur based in Bengaluru.
He runs Obii Kriationz Web LLP (web agency), hosts a podcast called Business Giseness, and is a BNI Highflyer chapter member.
His task pillars are: revenue, marketing, creation, networking, learning, recovery, operations, personal
Today's date is {TODAY}.
For each task line provided, analyse it and return a JSON object with these exact fields:
- "title": cleaned task title (fix capitalisation, remove filler words, max 100 chars)
- "original": the original line exactly as typed
- "pillar": one of [revenue, marketing, creation, networking, learning, recovery, operations, personal]
- "priority": one of [must, should, bonus]
- "tbcb_date": suggested "To Be Completed By" date in YYYY-MM-DD format. Pick a realistic date based on urgency. If task seems urgent, use today or tomorrow. If it's a routine task, pick 2-3 days out. If it's flexible/low-priority, pick 5-7 days out. Use null if no deadline makes sense.
- "value_score": integer 1-100 — estimated value/importance score. Critical revenue/client tasks = 70-100. Important operational tasks = 50-70. Nice-to-have tasks = 20-50. Low priority = 1-20.
- "notes": any additional context extracted from the line, or empty string
- "confidence": integer 1-10 — how confident you are in the categorisation
Return ONLY valid JSON in this exact format: { "tasks": [ ...array of task objects... ] }
No explanation. No markdown. No extra keys.
PROMPT;

        $systemPrompt = str_replace('{TODAY}', now()->toDateString(), $systemPrompt);

        $numbered = collect($filtered)->map(fn($l, $i) => ($i + 1) . '. ' . trim($l))->implode("\n");
        $userPrompt = "Categorise these tasks:\n" . $numbered;

        $start = microtime(true);

        $response = OpenAI::chat()->create([
            'model' => config('openai.default_model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'max_tokens' => 4000,
            'temperature' => 0.3,
            'response_format' => ['type' => 'json_object'],
        ]);

        $latency = round((microtime(true) - $start) * 1000);
        $content = $response->choices[0]->message->content;

        $parsed = json_decode($content, true);

        if (!$parsed || !isset($parsed['tasks']) || !is_array($parsed['tasks'])) {
            $tasks = collect($filtered)->map(fn($line) => [
                'title' => trim($line),
                'original' => $line,
                'pillar' => 'operations',
                'priority' => 'should',
                'tbcb_date' => now()->addDays(3)->toDateString(),
                'value_score' => 50,
                'confidence' => 1,
                'notes' => '',
            ])->all();
        } else {
            $tasks = $parsed['tasks'];
        }

        AILog::create([
            'feature' => 'custom',
            'prompt_tokens' => $response->usage->promptTokens,
            'completion_tokens' => $response->usage->completionTokens,
            'model' => 'gpt-4o-mini',
            'prompt_summary' => 'Brain dump: ' . count($filtered) . ' tasks',
            'response_summary' => 'Categorised ' . count($tasks) . ' tasks',
            'latency_ms' => $latency,
            'created_at' => now(),
        ]);

        return $tasks;
    }
}
