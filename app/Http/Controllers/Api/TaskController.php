<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Task;
use App\Models\WorkingDay;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'daily_plan_id' => 'required|exists:daily_plans,id',
            'title' => 'required|string|max:255',
            'priority' => 'sometimes|in:must,should,bonus',
            'time_block_id' => 'nullable|exists:time_blocks,id',
            'pillar' => 'nullable|string|max:40',
            'estimated_minutes' => 'nullable|integer|min:0|max:127',
        ]);

        $validated['sort_order'] = Task::where('daily_plan_id', $validated['daily_plan_id'])
                ->where('time_block_id', $validated['time_block_id'] ?? null)
                ->max('sort_order') + 1;

        $task = Task::create($validated);

        return response()->json($task->fresh()->toArray(), 201);
    }

    public function complete(Task $task): JsonResponse
    {
        $task->complete();

        return response()->json($task->toArray());
    }

    public function defer(Task $task): JsonResponse
    {
        $task->defer();

        return response()->json(['success' => true, 'message' => 'Task moved to tomorrow']);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['success' => true]);
    }

    public function updateFocus(Request $request, DailyPlan $plan): JsonResponse
    {
        $request->validate(['focus_intention' => 'nullable|string|max:255']);

        $plan->update(['focus_intention' => $request->input('focus_intention')]);

        return response()->json($plan->toArray());
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:tasks,id',
            'items.*.sort_order' => 'required|integer',
            'items.*.time_block_id' => 'nullable|integer',
        ]);

        foreach ($request->input('items') as $item) {
            $data = ['sort_order' => $item['sort_order']];
            if (array_key_exists('time_block_id', $item)) {
                $data['time_block_id'] = $item['time_block_id'];
            }
            Task::where('id', $item['id'])->update($data);
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'priority' => 'sometimes|in:must,should,bonus',
            'pillar' => 'nullable|string|max:40',
            'estimated_minutes' => 'nullable|integer|min:0|max:127',
            'time_block_id' => 'nullable|exists:time_blocks,id',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $task->update($validated);

        return response()->json($task->fresh()->toArray());
    }

    public function addSubTask(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'sometimes|in:must,should,bonus',
            'pillar' => 'nullable|string|max:40',
            'estimated_minutes' => 'nullable|integer|min:0|max:127',
        ]);

        $subTask = Task::create(array_merge($validated, [
            'daily_plan_id' => $task->daily_plan_id,
            'parent_task_id' => $task->id,
            'time_block_id' => $task->time_block_id,
            'sort_order' => $task->subTasks()->max('sort_order') + 1,
        ]));

        return response()->json($subTask->fresh()->toArray(), 201);
    }

    public function archive(Task $task): JsonResponse
    {
        $task->archive();

        return response()->json(['success' => true]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);

        $todayPlanId = DailyPlan::today()->id;

        $tasks = Task::active()
            ->whereNull('parent_task_id')
            ->where('is_completed', false)
            ->where('daily_plan_id', '!=', $todayPlanId)
            ->where('title', 'LIKE', '%' . $request->q . '%')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'title', 'pillar', 'priority', 'estimated_minutes', 'daily_plan_id']);

        return response()->json($tasks);
    }

    public function pullToToday(Request $request, Task $task): JsonResponse
    {
        $plan = DailyPlan::today();
        $task->update([
            'daily_plan_id' => $plan->id,
            'time_block_id' => null,
        ]);

        return response()->json($task->fresh()->toArray());
    }

    public function rolloverToday(Request $request): JsonResponse
    {
        $plan = DailyPlan::today();

        $incompleteTasks = $plan->tasks()
            ->where('is_completed', false)
            ->whereNull('archived_at')
            ->whereNull('parent_task_id')
            ->get();

        if ($incompleteTasks->isEmpty()) {
            return response()->json([
                'success' => true,
                'rolled' => 0,
                'message' => 'No incomplete tasks to roll over.',
            ]);
        }

        $tomorrow = now()->addDay()->toDateString();
        $tomorrowDay = WorkingDay::where('day_number', now()->addDay()->dayOfWeek)->first();
        $tomorrowPlan = DailyPlan::firstOrCreate(
            ['plan_date' => $tomorrow],
            ['working_day_id' => $tomorrowDay?->id]
        );

        $rolled = 0;
        foreach ($incompleteTasks as $task) {
            $newCount = $task->rollover_count + 1;

            Task::create([
                'daily_plan_id' => $tomorrowPlan->id,
                'time_block_id' => null,
                'title' => $task->title,
                'notes' => $task->notes,
                'pillar' => $task->pillar,
                'priority' => $newCount >= 3 ? 'must' : $task->priority,
                'estimated_minutes' => $task->estimated_minutes,
                'is_completed' => false,
                'is_rolled_over' => true,
                'rolled_from_date' => now()->toDateString(),
                'rollover_count' => $newCount,
                'sort_order' => 0,
                'parent_task_id' => null,
            ]);

            $task->archive();
            $rolled++;
        }

        return response()->json([
            'success' => true,
            'rolled' => $rolled,
            'message' => $rolled . ' task' . ($rolled !== 1 ? 's' : '') . ' rolled over to tomorrow.',
        ]);
    }

    public function reassign(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);
        $workingDay = WorkingDay::where('day_number', $date->dayOfWeek)->first();

        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => $date->toDateString()],
            ['working_day_id' => $workingDay?->id]
        );

        $task->update(['daily_plan_id' => $plan->id]);

        return response()->json([
            'success' => true,
            'message' => 'Task moved to ' . $date->format('D, j M'),
        ]);
    }

    public function setRecurring(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'is_recurring' => 'required|boolean',
            'recurring_days' => 'nullable|array',
            'recurring_days.*' => 'integer|min:0|max:6',
            'recurring_type' => 'nullable|in:daily,weekly,theme_day',
        ]);

        $task->update($validated);

        return response()->json($task->fresh()->toArray());
    }
}
