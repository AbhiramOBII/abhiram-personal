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
His 7 day themes are:
- Sunday: Recovery + Vision
- Monday: Revenue & Operations
- Tuesday: Marketing & Funnel
- Wednesday: Deep Creation
- Thursday: BNI + Full Networking
- Friday: Shoot & Media
- Saturday: Podcast + Community
His task pillars are: revenue, marketing, creation, networking, learning, recovery, operations, personal
For each task line provided, analyse it and return a JSON object with these exact fields:
- "title": cleaned task title (fix capitalisation, remove filler words, max 100 chars)
- "original": the original line exactly as typed
- "pillar": one of [revenue, marketing, creation, networking, learning, recovery, operations, personal]
- "priority": one of [must, should, bonus]
- "suggested_day": day of week as lowercase string [sunday, monday, tuesday, wednesday, thursday, friday, saturday] — pick based on his day themes
- "estimated_minutes": integer — realistic time estimate, one of [15, 30, 45, 60, 90, 120]
- "notes": any additional context extracted from the line, or empty string
- "confidence": integer 1-10 — how confident you are in the categorisation
Return ONLY valid JSON in this exact format: { "tasks": [ ...array of task objects... ] }
No explanation. No markdown. No extra keys.
PROMPT;

        $numbered = collect($filtered)->map(fn($l, $i) => ($i + 1) . '. ' . trim($l))->implode("\n");
        $userPrompt = "Categorise these tasks:\n" . $numbered;

        $start = microtime(true);

        $response = OpenAI::chat()->create([
            'model' => config('openai.default_model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'max_tokens' => 2000,
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
                'suggested_day' => 'monday',
                'estimated_minutes' => 30,
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
