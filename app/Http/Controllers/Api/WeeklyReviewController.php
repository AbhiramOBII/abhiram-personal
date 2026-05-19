<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeeklyReviewController extends Controller
{
    public function update(Request $request, WeeklyReview $review): JsonResponse
    {
        $validated = $request->validate([
            'reflection_win' => 'nullable|string',
            'reflection_challenge' => 'nullable|string',
            'reflection_learning' => 'nullable|string',
            'reflection_gratitude' => 'nullable|string',
            'next_week_focus' => 'nullable|string|max:255',
            'next_week_priorities' => 'nullable|array|max:3',
            'next_week_priorities.*' => 'nullable|string|max:255',
            'identity_score' => 'nullable|integer|min:1|max:10',
            'identity_note' => 'nullable|string',
            'energy_rating' => 'nullable|integer|min:1|max:10',
        ]);

        $review->update($validated);

        return response()->json(['success' => true]);
    }

    public function complete(WeeklyReview $review): JsonResponse
    {
        $review->complete();

        return response()->json(['success' => true, 'message' => 'Week closed. On to the next one.']);
    }
}
