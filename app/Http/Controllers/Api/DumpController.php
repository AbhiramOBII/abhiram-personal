<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Task;
use App\Models\WorkingDay;
use App\Services\DumpService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DumpController extends Controller
{
    public function categorise(Request $request)
    {
        $request->validate([
            'lines' => 'required|array|max:50',
            'lines.*' => 'string|max:300',
        ]);

        try {
            $service = new DumpService();
            $tasks = $service->categorise($request->lines);

            return response()->json($tasks);
        } catch (\Exception $e) {
            return response()->json(['error' => 'AI categorisation failed. Please try again.'], 500);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array|max:50',
        ]);

        $dayMap = [
            'sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
            'thursday' => 4, 'friday' => 5, 'saturday' => 6,
        ];

        $created = 0;

        foreach ($request->tasks as $task) {
            $title = trim($task['title'] ?? '');
            if (empty($title)) {
                continue;
            }

            if (isset($task['selected']) && $task['selected'] === false) {
                continue;
            }

            $targetDay = $dayMap[$task['suggested_day'] ?? 'monday'] ?? 1;
            $date = Carbon::now()->dayOfWeek === $targetDay
                ? Carbon::today()
                : Carbon::now()->next($targetDay);

            $workingDay = WorkingDay::where('day_number', $date->dayOfWeek)->first();

            $dailyPlan = DailyPlan::firstOrCreate(
                ['plan_date' => $date->toDateString()],
                ['working_day_id' => $workingDay?->id]
            );

            Task::create([
                'title' => $title,
                'notes' => $task['notes'] ?? null,
                'pillar' => $task['pillar'] ?? null,
                'priority' => $task['priority'] ?? 'should',
                'estimated_minutes' => $task['estimated_minutes'] ?? 30,
                'daily_plan_id' => $dailyPlan->id,
                'is_completed' => false,
                'rollover_count' => 0,
            ]);

            $created++;
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'message' => $created . ' tasks added to your schedule.',
        ]);
    }
}
