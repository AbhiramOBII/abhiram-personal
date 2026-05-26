<?php

namespace App\Services;

use App\Models\AiLog;
use App\Models\WorkingDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DayPlanService
{
    public function generateRationale(Collection $topTasks, WorkingDay $workingDay, Carbon $date): string
    {
        if ($topTasks->isEmpty()) {
            return '';
        }

        $taskLines = $topTasks->take(3)->map(function ($task, $i) {
            return ($i + 1) . ". \"{$task->title}\" — Value Score: {$task->value_score}, Pillar: {$task->pillar}, Status: {$task->status}" .
                ($task->deadline_at ? ", Deadline: {$task->deadline_formatted}" : '');
        })->implode("\n");

        $systemPrompt = "You are Abhiram Chandramohan's personal productivity coach. He is a founder in Bengaluru running Obii Kriationz Web LLP and hosting the Business Giseness podcast. Write a sharp, personal, 2–3 sentence morning briefing for his top 3 tasks today. Be specific about which block to start in. No generic advice.";

        $userPrompt = "Today is {$workingDay->theme} day ({$date->format('l, d M')}).\n\nTop 3 tasks by value score:\n{$taskLines}\n\nWrite the morning briefing now.";

        try {
            $start = microtime(true);

            $response = \OpenAI::chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'max_tokens'  => 120,
                'temperature' => 0.75,
            ]);

            $latency = round((microtime(true) - $start) * 1000);
            $message = trim($response->choices[0]->message->content);

            AiLog::create([
                'feature'           => 'custom',
                'prompt_tokens'     => $response->usage->promptTokens,
                'completion_tokens' => $response->usage->completionTokens,
                'model'             => 'gpt-4o-mini',
                'prompt_summary'    => 'Day plan rationale for ' . $date->toDateString(),
                'response_summary'  => substr($message, 0, 120),
                'latency_ms'        => $latency,
            ]);

            return $message;
        } catch (\Exception $e) {
            $top = $topTasks->first();
            return "Start your {$workingDay->theme} day with \"{$top->title}\" (Value Score: {$top->value_score}). It scores highest today based on impact, urgency, and theme alignment.";
        }
    }
}
