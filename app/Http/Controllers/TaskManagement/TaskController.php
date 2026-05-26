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
        $status = $request->get('status');
        $type = $request->get('type');

        $query = Task::active()->whereNull('parent_task_id')->with(['dailyPlan.workingDay', 'subTasks']);

        // Type filter
        if ($type === 'project') {
            $query->project();
        } elseif ($type === 'daily') {
            $query->daily();
        }

        // Only apply date range for non-project views
        if ($type !== 'project') {
            if ($range === 'week') {
                $weekStart = now()->startOfWeek();
                $weekEnd = now()->endOfWeek();
                $query->where(function ($q) use ($weekStart, $weekEnd) {
                    $q->whereHas('dailyPlan', fn($q2) => $q2->whereBetween('plan_date', [$weekStart, $weekEnd]))
                      ->orWhereNull('tbcb_date')
                      ->orWhereBetween('tbcb_date', [$weekStart, $weekEnd]);
                });
            } elseif ($range === '7days') {
                $query->whereHas('dailyPlan', fn($q) => $q->where('plan_date', '>=', now()->subDays(7)->toDateString()));
            } elseif ($range === 'month') {
                $query->whereHas('dailyPlan', fn($q) => $q->where('plan_date', '>=', now()->startOfMonth()->toDateString()));
            }
        }

        if ($pillar) {
            $query->where('pillar', $pillar);
        }

        if ($status && in_array($status, ['backlog', 'wip', 'done', 'deferred'])) {
            $query->where('status', $status);
        }

        $sort = $request->get('sort');

        if ($sort === 'value_score') {
            $tasks = $query->orderByDesc('value_score')->get();
        } elseif ($type === 'project') {
            $tasks = $query->orderBy('deadline_at')->get();
        } else {
            $tasks = $query->orderByDesc(
                DailyPlan::select('plan_date')->whereColumn('daily_plans.id', 'tasks.daily_plan_id')
            )->get();
        }

        $grouped = $tasks->groupBy(fn($t) => $t->dailyPlan?->plan_date?->toDateString() ?? 'no-plan');

        // Kanban data — group all tasks by status
        $kanbanTasks = $tasks->groupBy('status')->map(fn($g) => $g->values()->toArray());

        $days = WorkingDay::ordered()->get()->keyBy('id');
        $timeBlocks = TimeBlock::orderBy('sort_order')->get();
        $statusConfig = Task::statusConfig();

        // Project tasks grouped by deadline proximity for timeline view
        $projectTimeline = $type === 'project'
            ? $tasks->groupBy(fn($t) => $t->deadline_proximity)
            : collect();

        return view('task-management.index', compact('grouped', 'days', 'range', 'pillar', 'status', 'type', 'timeBlocks', 'kanbanTasks', 'statusConfig', 'projectTimeline'));
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

    public function sampleCsv()
    {
        $csv = "title,priority,pillar,estimated_minutes,date,time_block\n";
        $csv .= "Write blog post about AI tools,must,content,45," . now()->format('Y-m-d') . ",\n";
        $csv .= "Review analytics dashboard,should,marketing,20," . now()->format('Y-m-d') . ",\n";
        $csv .= "Record podcast episode,must,podcast,60," . now()->addDay()->format('Y-m-d') . ",\n";
        $csv .= "Gym session,should,health,45," . now()->format('Y-m-d') . ",\n";
        $csv .= "Reply to partnership emails,bonus,networking,15," . now()->format('Y-m-d') . ",\n";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="dayos-tasks-sample.csv"');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:512',
        ]);

        $file = $request->file('csv_file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_map('strtolower', array_map('trim', array_shift($rows)));

        $requiredColumns = ['title'];
        foreach ($requiredColumns as $col) {
            if (!in_array($col, $header)) {
                return redirect()->back()->with('error', "CSV must have a '{$col}' column.");
            }
        }

        $timeBlockMap = TimeBlock::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);
        $validPriorities = ['must', 'should', 'bonus'];
        $created = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            }
            $data = array_combine($header, array_map('trim', $row));

            if (empty($data['title'])) {
                continue;
            }

            $date = !empty($data['date']) ? $data['date'] : now()->toDateString();
            try {
                $parsedDate = \Carbon\Carbon::parse($date)->toDateString();
            } catch (\Exception $e) {
                $errors[] = "Row " . ($i + 2) . ": Invalid date '{$date}'.";
                continue;
            }

            $dayOfWeek = \Carbon\Carbon::parse($parsedDate)->dayOfWeek;
            $workingDay = WorkingDay::where('day_number', $dayOfWeek)->first();

            $plan = DailyPlan::firstOrCreate(
                ['plan_date' => $parsedDate],
                ['working_day_id' => $workingDay?->id]
            );

            $priority = !empty($data['priority']) && in_array(strtolower($data['priority']), $validPriorities)
                ? strtolower($data['priority'])
                : 'should';

            $timeBlockId = null;
            if (!empty($data['time_block'])) {
                $timeBlockId = $timeBlockMap[strtolower($data['time_block'])] ?? null;
            }

            Task::create([
                'daily_plan_id' => $plan->id,
                'title' => $data['title'],
                'priority' => $priority,
                'pillar' => !empty($data['pillar']) ? strtolower($data['pillar']) : null,
                'estimated_minutes' => !empty($data['estimated_minutes']) ? (int) $data['estimated_minutes'] : null,
                'time_block_id' => $timeBlockId,
                'sort_order' => 0,
            ]);
            $created++;
        }

        $message = "{$created} task(s) imported successfully.";
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' row(s) skipped.';
        }

        return redirect()->back()->with('success', $message)->with('import_errors', $errors);
    }
}
