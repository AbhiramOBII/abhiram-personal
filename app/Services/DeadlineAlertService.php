<?php

namespace App\Services;

use App\Models\AILog;
use App\Models\DeadlineAlert;
use App\Models\Task;

class DeadlineAlertService
{
    public function generateAlert(Task $task): string
    {
        $proximity = $task->deadline_proximity;
        if (!$proximity) return '';

        $deadlineFormatted = $task->deadline_at->format('l, d M Y \a\t g:i A');
        $daysLeft = match ($proximity) {
            'overdue' => 'The deadline has passed.',
            '0d'      => 'The deadline is TODAY.',
            '1d'      => 'The deadline is TOMORROW.',
            '3d'      => 'The deadline is in ' . (int) now()->startOfDay()->diffInDays($task->deadline_at->startOfDay(), false) . ' days.',
            default   => '',
        };

        $systemPrompt = "You are a productivity coach for Abhiram Chandramohan, a founder in Bengaluru who runs Obii Kriationz Web LLP and hosts the Business Giseness podcast. Write a short, direct deadline warning message. Be personal, specific, and action-oriented. Maximum 2 sentences. No fluff.";
        $userPrompt = "Task: \"{$task->title}\"\nDeadline: {$deadlineFormatted}\n{$daysLeft}\nNotes: {$task->deadline_notes}\nCurrent status: {$task->status}\n\nWrite a deadline warning message for Abhiram's dashboard.";

        try {
            $start = microtime(true);
            $response = \OpenAI::chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
                'max_tokens'  => 80,
                'temperature' => 0.7,
            ]);
            $latency = (int) ((microtime(true) - $start) * 1000);

            $message = trim($response->choices[0]->message->content);

            AILog::create([
                'feature'            => 'custom',
                'prompt_tokens'      => $response->usage->promptTokens,
                'completion_tokens'  => $response->usage->completionTokens,
                'model'              => 'gpt-4o-mini',
                'prompt_summary'     => 'Deadline alert: ' . $task->title,
                'response_summary'   => substr($message, 0, 100),
                'latency_ms'         => $latency,
            ]);

            return $message;
        } catch (\Exception $e) {
            return match ($proximity) {
                'overdue' => "\"{$task->title}\" is overdue. Update the deadline or mark it done.",
                '0d'      => "\"{$task->title}\" is due today at {$task->deadline_at->format('g:i A')}. Prioritise it now.",
                '1d'      => "\"{$task->title}\" is due tomorrow. Make sure it's on track.",
                '3d'      => "\"{$task->title}\" is due in " . (int) now()->startOfDay()->diffInDays($task->deadline_at->startOfDay(), false) . " days. Check your progress.",
                default   => '',
            };
        }
    }

    public function processTask(Task $task): void
    {
        $proximity = $task->deadline_proximity;
        if (!$proximity) return;

        $flagMap = [
            '3d'      => 'deadline_notified_3d',
            '1d'      => 'deadline_notified_1d',
            '0d'      => 'deadline_notified_0d',
            'overdue' => null,
        ];

        $flag = $flagMap[$proximity] ?? null;

        // Skip if already notified for this tier (except overdue — always alert)
        if ($flag && $task->$flag) return;

        // Skip if already alerted today for this type
        $existsToday = DeadlineAlert::where('task_id', $task->id)
            ->where('alert_type', $proximity)
            ->where('alert_date', today())
            ->exists();
        if ($existsToday) return;

        $message = $this->generateAlert($task);
        if (!$message) return;

        DeadlineAlert::create([
            'task_id'    => $task->id,
            'alert_type' => $proximity,
            'ai_message' => $message,
            'alert_date' => today(),
        ]);

        if ($flag) {
            $task->update([$flag => true]);
        }
    }
}
