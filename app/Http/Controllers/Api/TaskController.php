<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Task;
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
        ]);

        foreach ($request->input('items') as $item) {
            Task::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
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
