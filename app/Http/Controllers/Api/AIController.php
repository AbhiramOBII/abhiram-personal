<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\WeeklyReview;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;

class AIController extends Controller
{
    public function __construct(protected AIService $service) {}

    public function dailyBriefing(): JsonResponse
    {
        $plan = DailyPlan::today();
        $briefing = $this->service->getDailyBriefing($plan, bustCache: true);

        return response()->json(['briefing' => $briefing]);
    }

    public function taskSuggestions(): JsonResponse
    {
        $plan = DailyPlan::today();
        $suggestions = $this->service->getTaskSuggestions($plan, bustCache: true);

        return response()->json(['suggestions' => $suggestions]);
    }

    public function weeklyInsight(WeeklyReview $review): JsonResponse
    {
        $insight = $this->service->getWeeklyInsight($review, bustCache: true);

        return response()->json(['insight' => $insight]);
    }

    public function patternInsight(): JsonResponse
    {
        $insight = $this->service->getPatternInsight(bustCache: true);

        return response()->json(['insight' => $insight]);
    }
}
