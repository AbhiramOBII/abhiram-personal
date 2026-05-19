<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\LearningItem;
use App\Models\LearningSession;
use App\Models\SkillDomain;
use App\Models\WorkingDay;

class UpskillingService
{
    public function getTodaySuggestion(): ?LearningItem
    {
        $workingDay = WorkingDay::today();
        $focus = $workingDay?->upskill_focus;

        if ($focus) {
            $domain = SkillDomain::active()
                ->where('name', 'LIKE', '%' . $focus . '%')
                ->first();

            if (!$domain) {
                $domains = SkillDomain::active()->get();
                foreach ($domains as $d) {
                    if (stripos($d->name, $focus) !== false || stripos($focus, $d->name) !== false) {
                        $domain = $d;
                        break;
                    }
                }
            }

            if ($domain) {
                $item = LearningItem::active()
                    ->pending()
                    ->forDomain($domain->id)
                    ->orderByDesc('priority')
                    ->first();

                if ($item) {
                    return $item;
                }
            }
        }

        return LearningItem::active()
            ->pending()
            ->orderByDesc('priority')
            ->first();
    }

    public function startSession(LearningItem $item, DailyPlan $plan): LearningSession
    {
        return LearningSession::create([
            'learning_item_id' => $item->id,
            'skill_domain_id' => $item->skill_domain_id,
            'daily_plan_id' => $plan->id,
            'session_date' => now()->toDateString(),
            'started_at' => now(),
        ]);
    }

    public function endSession(LearningSession $session, ?int $durationMinutes = null, ?string $takeaway = null, ?string $notes = null): LearningSession
    {
        $session->ended_at = now();

        if ($durationMinutes !== null) {
            $session->duration_minutes = $durationMinutes;
        } elseif ($session->started_at) {
            $session->duration_minutes = (int) $session->started_at->diffInMinutes(now());
        }

        if ($takeaway !== null) {
            $session->takeaway = $takeaway;
        }

        if ($notes !== null) {
            $session->notes = $notes;
        }

        $session->save();

        return $session;
    }

    public function getTodayMinutes(): int
    {
        return (int) LearningSession::where('session_date', now()->toDateString())
            ->sum('duration_minutes');
    }

    public function getWeekMinutes(): int
    {
        $monday = now()->startOfWeek()->toDateString();
        $today = now()->toDateString();

        return (int) LearningSession::whereBetween('session_date', [$monday, $today])
            ->sum('duration_minutes');
    }

    public function getMonthMinutes(): int
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $today = now()->toDateString();

        return (int) LearningSession::whereBetween('session_date', [$startOfMonth, $today])
            ->sum('duration_minutes');
    }

    public function getTotalMinutes(): int
    {
        return (int) LearningSession::sum('duration_minutes');
    }
}
