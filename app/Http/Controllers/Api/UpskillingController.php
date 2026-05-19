<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\LearningItem;
use App\Models\LearningSession;
use App\Services\UpskillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpskillingController extends Controller
{
    public function __construct(protected UpskillingService $service) {}

    public function startSession(Request $request): JsonResponse
    {
        $request->validate([
            'learning_item_id' => 'required|exists:learning_items,id',
        ]);

        $item = LearningItem::findOrFail($request->input('learning_item_id'));
        $plan = DailyPlan::today();

        $session = $this->service->startSession($item, $plan);
        $session->load(['learningItem', 'skillDomain']);

        return response()->json($session);
    }

    public function endSession(Request $request, LearningSession $session): JsonResponse
    {
        $request->validate([
            'duration_minutes' => 'nullable|integer|min:1',
            'takeaway' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $session = $this->service->endSession(
            $session,
            $request->input('duration_minutes'),
            $request->input('takeaway'),
            $request->input('notes')
        );

        return response()->json($session);
    }

    public function updateNotes(Request $request, LearningSession $session): JsonResponse
    {
        $request->validate(['notes' => 'nullable|string']);

        $session->update(['notes' => $request->input('notes')]);

        return response()->json(['success' => true]);
    }

    public function updateTakeaway(Request $request, LearningSession $session): JsonResponse
    {
        $request->validate(['takeaway' => 'nullable|string']);

        $session->update(['takeaway' => $request->input('takeaway')]);

        return response()->json(['success' => true]);
    }
}
