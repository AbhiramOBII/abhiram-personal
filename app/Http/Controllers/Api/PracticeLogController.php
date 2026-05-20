<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Practice;
use App\Models\PracticeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PracticeLogController extends Controller
{
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'practice_id' => 'required|exists:practices,id',
            'is_completed' => 'required|boolean',
            'date' => 'required|date',
        ]);

        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => $request->date],
            ['working_day_id' => null]
        );

        $log = PracticeLog::firstOrCreate(
            ['practice_id' => $request->practice_id, 'logged_date' => $request->date],
            ['daily_plan_id' => $plan->id, 'is_completed' => false]
        );

        $log->update([
            'is_completed' => $request->is_completed,
            'completed_at' => $request->is_completed ? now() : null,
        ]);

        return response()->json(['success' => true]);
    }

    public function saveResponse(Request $request): JsonResponse
    {
        $request->validate([
            'practice_id' => 'required|exists:practices,id',
            'response_text' => 'nullable|string|max:5000',
            'date' => 'required|date',
        ]);

        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => $request->date],
            ['working_day_id' => null]
        );

        $log = PracticeLog::firstOrCreate(
            ['practice_id' => $request->practice_id, 'logged_date' => $request->date],
            ['daily_plan_id' => $plan->id, 'is_completed' => false]
        );

        $log->update([
            'response_text' => $request->response_text,
            'is_completed' => !empty($request->response_text),
            'completed_at' => !empty($request->response_text) ? now() : null,
        ]);

        return response()->json(['success' => true]);
    }

    public function saveQuantity(Request $request): JsonResponse
    {
        $request->validate([
            'practice_id' => 'required|exists:practices,id',
            'quantity' => 'required|integer|min:0',
            'date' => 'required|date',
        ]);

        $practice = Practice::findOrFail($request->practice_id);

        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => $request->date],
            ['working_day_id' => null]
        );

        $log = PracticeLog::firstOrCreate(
            ['practice_id' => $request->practice_id, 'logged_date' => $request->date],
            ['daily_plan_id' => $plan->id, 'is_completed' => false]
        );

        $isCompleted = $practice->target_value && $request->quantity >= $practice->target_value;

        $log->update([
            'quantity' => $request->quantity,
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? now() : null,
        ]);

        return response()->json(['success' => true, 'auto_completed' => $isCompleted]);
    }
}
