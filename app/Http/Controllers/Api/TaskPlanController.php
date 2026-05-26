<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Task;
use App\Models\TimeBlock;
use App\Models\WorkingDay;
use App\Services\DayPlanService;
use App\Services\ValueScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskPlanController extends Controller
{
    public function planDay(Request $request, ValueScoreService $scorer, DayPlanService $planner): JsonResponse
    {
        $date = today();
        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => $date->toDateString()],
            ['working_day_id' => WorkingDay::today()?->id]
        );
        $workingDay = WorkingDay::today();

        // Recalculate all active task scores for today
        $tasks = Task::whereIn('status', ['backlog', 'wip'])
            ->where('daily_plan_id', $plan->id)
            ->get();

        foreach ($tasks as $task) {
            $scorer->calculateAndSave($task, $date);
        }

        $tasks = Task::whereIn('status', ['backlog', 'wip'])
            ->where('daily_plan_id', $plan->id)
            ->orderByDesc('value_score')
            ->get();

        // Get time slots for today
        $timeSlots = $workingDay
            ? TimeBlock::where('working_day_id', $workingDay->id)->orderBy('start_time')->get()
            : collect();

        // Generate slot assignment
        $assignment = $scorer->assignToSlots($tasks, $timeSlots);

        // Generate AI rationale for top 3
        $rationale = $workingDay ? $planner->generateRationale($tasks->take(3), $workingDay, $date) : '';

        // Check resurface candidates
        $resurfaceCandidates = $scorer->getResurfaceCandidates($tasks->count(), $date);

        return response()->json([
            'success'              => true,
            'tasks'                => $tasks->map(fn($t) => [
                'id'                => $t->id,
                'title'             => $t->title,
                'value_score'       => $t->value_score,
                'urgency_score'     => $t->urgency_score,
                'efficiency_score'  => $t->efficiency_score,
                'theme_score'       => $t->theme_score,
                'impact_rating'     => $t->impact_rating,
                'status'            => $t->status,
                'pillar'            => $t->pillar,
                'priority'          => $t->priority,
                'estimated_minutes' => $t->estimated_minutes,
                'deadline_formatted' => $t->deadline_formatted,
                'deadline_badge'    => $t->deadline_badge,
                'score_badge'       => $t->score_badge,
            ]),
            'assignment'           => $assignment,
            'rationale'            => $rationale,
            'resurface_candidates' => $resurfaceCandidates->map(fn($t) => [
                'id'          => $t->id,
                'title'       => $t->title,
                'value_score' => $t->value_score,
                'status'      => $t->status,
                'pillar'      => $t->pillar,
            ]),
        ]);
    }

    public function confirmPlan(Request $request, ValueScoreService $scorer): JsonResponse
    {
        $request->validate([
            'task_order'           => 'array',
            'task_order.*'         => 'integer|exists:tasks,id',
            'resurface_task_ids'   => 'array',
            'resurface_task_ids.*' => 'integer|exists:tasks,id',
        ]);

        foreach ($request->task_order ?? [] as $index => $taskId) {
            Task::where('id', $taskId)->update(['sort_order' => $index + 1]);
        }

        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => today()->toDateString()],
            ['working_day_id' => WorkingDay::today()?->id]
        );

        foreach ($request->resurface_task_ids ?? [] as $taskId) {
            $task = Task::find($taskId);
            if ($task) {
                $scorer->resurface($task, $plan->id, today());
            }
        }

        return response()->json(['success' => true, 'message' => 'Day plan confirmed.']);
    }

    public function updateImpact(Request $request, Task $task, ValueScoreService $scorer): JsonResponse
    {
        $request->validate(['impact_rating' => 'required|integer|min:0|max:4']);

        $task->update(['impact_rating' => $request->impact_rating]);
        $scorer->calculateAndSave($task, today());

        return response()->json([
            'success'     => true,
            'value_score' => $task->fresh()->value_score,
            'score_badge' => $task->fresh()->score_badge,
        ]);
    }

    public function autoSort(Request $request): JsonResponse
    {
        $plan = DailyPlan::firstOrCreate(
            ['plan_date' => today()->toDateString()],
            ['working_day_id' => WorkingDay::today()?->id]
        );

        $tasks = Task::whereIn('status', ['backlog', 'wip'])
            ->where('daily_plan_id', $plan->id)
            ->orderByDesc('value_score')
            ->get();

        foreach ($tasks as $index => $task) {
            $task->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Tasks sorted by value score.']);
    }
}
