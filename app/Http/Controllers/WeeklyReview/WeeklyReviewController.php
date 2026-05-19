<?php

namespace App\Http\Controllers\WeeklyReview;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReview;
use App\Services\AIService;
use App\Services\WeeklyReviewService;
use Illuminate\Http\Request;

class WeeklyReviewController extends Controller
{
    public function __construct(protected WeeklyReviewService $service) {}

    public function index(AIService $aiService)
    {
        $review = WeeklyReview::currentWeek();
        $stats = $this->service->getWeekStats($review);
        $identityPrompt = $this->service->getIdentityPrompt($review);
        $suggestions = $this->service->generateNextWeekSuggestions($review);
        $aiInsight = $aiService->getWeeklyInsight($review);

        return view('weekly-review.index', compact('review', 'stats', 'identityPrompt', 'suggestions', 'aiInsight'));
    }

    public function history()
    {
        $reviews = WeeklyReview::where('is_completed', true)
            ->orderByDesc('week_start')
            ->get();

        $avgCompletion = $reviews->count() > 0
            ? (int) round($reviews->avg(fn($r) => $this->service->getWeekStats($r)['completion_rate']))
            : 0;

        $avgIdentityScore = $reviews->whereNotNull('identity_score')->count() > 0
            ? round($reviews->whereNotNull('identity_score')->avg('identity_score'), 1)
            : 0;

        $totalUpskillHours = round(
            $reviews->sum(fn($r) => $this->service->getWeekStats($r)['upskill_minutes']) / 60,
            1
        );

        return view('weekly-review.history', compact('reviews', 'avgCompletion', 'avgIdentityScore', 'totalUpskillHours'));
    }
}
