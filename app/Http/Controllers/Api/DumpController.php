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

        $created = 0;

        foreach ($request->tasks as $task) {
            $title = trim($task['title'] ?? '');
            if (empty($title)) {
                continue;
            }

            if (isset($task['selected']) && $task['selected'] === false) {
                continue;
            }

            $tbcbDate = !empty($task['tbcb_date']) ? Carbon::parse($task['tbcb_date'])->toDateString() : null;
            $valueScore = isset($task['value_score']) ? (int) $task['value_score'] : null;

            Task::create([
                'title' => $title,
                'notes' => $task['notes'] ?? null,
                'pillar' => $task['pillar'] ?? null,
                'priority' => $task['priority'] ?? 'should',
                'tbcb_date' => $tbcbDate,
                'value_score' => $valueScore,
                'status' => 'backlog',
                'is_completed' => false,
                'rollover_count' => 0,
            ]);

            $created++;
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'message' => $created . ' tasks added.',
        ]);
    }
}
