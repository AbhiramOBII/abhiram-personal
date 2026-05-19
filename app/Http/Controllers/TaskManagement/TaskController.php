<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\TimeBlock;
use App\Models\WorkingDay;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', 'week');
        $pillar = $request->get('pillar');

        $query = Task::active()->whereNull('parent_task_id')->with(['dailyPlan.workingDay', 'subTasks']);

        if ($range === 'week') {
            $query->whereHas('dailyPlan', fn($q) => $q->whereBetween('plan_date', [now()->startOfWeek(), now()->endOfWeek()]));
        } elseif ($range === '7days') {
            $query->whereHas('dailyPlan', fn($q) => $q->where('plan_date', '>=', now()->subDays(7)->toDateString()));
        } elseif ($range === 'month') {
            $query->whereHas('dailyPlan', fn($q) => $q->where('plan_date', '>=', now()->startOfMonth()->toDateString()));
        }

        if ($pillar) {
            $query->where('pillar', $pillar);
        }

        $tasks = $query->orderByDesc(
            DailyPlan::select('plan_date')->whereColumn('daily_plans.id', 'tasks.daily_plan_id')
        )->get();

        $grouped = $tasks->groupBy(fn($t) => $t->dailyPlan->plan_date->toDateString());

        $days = WorkingDay::ordered()->get()->keyBy('id');
        $timeBlocks = TimeBlock::orderBy('sort_order')->get();

        return view('task-management.index', compact('grouped', 'days', 'range', 'pillar', 'timeBlocks'));
    }

    public function templates()
    {
        $templates = TaskTemplate::with(['workingDay', 'timeBlock'])->orderBy('sort_order')->get();
        $groupedTemplates = $templates->groupBy(fn($t) => $t->working_day_id ?? 'any');
        $workingDays = WorkingDay::ordered()->get();
        $timeBlocks = TimeBlock::orderBy('sort_order')->get();

        return view('task-management.templates', compact('groupedTemplates', 'workingDays', 'timeBlocks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'daily_plan_id' => 'required|exists:daily_plans,id',
            'title' => 'required|string|max:255',
            'priority' => 'sometimes|in:must,should,bonus',
            'time_block_id' => 'nullable|exists:time_blocks,id',
            'pillar' => 'nullable|string|max:40',
            'estimated_minutes' => 'nullable|integer|min:0|max:127',
            'parent_task_id' => 'nullable|exists:tasks,id',
        ]);

        $task = Task::create($validated);

        return redirect()->back()->with('success', 'Task created.');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'priority' => 'sometimes|in:must,should,bonus',
            'pillar' => 'nullable|string|max:40',
            'estimated_minutes' => 'nullable|integer|min:0|max:127',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Task updated.');
    }

    public function archive(Task $task)
    {
        $task->archive();

        return redirect()->back()->with('success', 'Task archived.');
    }

    public function setRecurring(Request $request, Task $task)
    {
        $validated = $request->validate([
            'is_recurring' => 'required|boolean',
            'recurring_days' => 'nullable|array',
            'recurring_days.*' => 'integer|min:0|max:6',
            'recurring_type' => 'nullable|in:daily,weekly,theme_day',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Recurring settings updated.');
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'working_day_id' => 'nullable|exists:working_days,id',
            'time_block_id' => 'nullable|exists:time_blocks,id',
            'title' => 'required|string|max:255',
            'pillar' => 'nullable|string|max:40',
            'priority' => 'sometimes|in:must,should,bonus',
            'estimated_minutes' => 'nullable|integer|min:0|max:127',
        ]);

        TaskTemplate::create($validated);

        return redirect()->back()->with('success', 'Template created.');
    }

    public function toggleTemplate(TaskTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        return redirect()->back()->with('success', 'Template ' . ($template->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroyTemplate(TaskTemplate $template)
    {
        $template->delete();

        return redirect()->back()->with('success', 'Template deleted.');
    }
}
