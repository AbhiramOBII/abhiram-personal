<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NudgeLog;
use App\Services\NudgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NudgeController extends Controller
{
    public function index(NudgeService $service): JsonResponse
    {
        return response()->json($service->getActiveNudges());
    }

    public function dismiss(Request $request): JsonResponse
    {
        $request->validate([
            'nudge_type' => 'required|string',
            'context_key' => 'required|string',
        ]);

        NudgeLog::updateOrCreate(
            [
                'nudge_type' => $request->nudge_type,
                'context_key' => $request->context_key,
                'shown_date' => now()->toDateString(),
            ],
            ['dismissed_at' => now()]
        );

        return response()->json(['success' => true]);
    }

    public function click(Request $request): JsonResponse
    {
        $request->validate([
            'nudge_type' => 'required|string',
            'context_key' => 'required|string',
        ]);

        NudgeLog::updateOrCreate(
            [
                'nudge_type' => $request->nudge_type,
                'context_key' => $request->context_key,
                'shown_date' => now()->toDateString(),
            ],
            ['clicked_at' => now()]
        );

        return response()->json(['success' => true]);
    }
}
