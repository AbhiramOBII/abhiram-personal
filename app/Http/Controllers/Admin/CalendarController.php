<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarDay;
use App\Models\CalendarTask;
use App\Models\WorkingDay;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function today()
    {
        $today = Carbon::today();
        $calendarDay = CalendarDay::where('date', $today->toDateString())->first();

        $workingDay = WorkingDay::where('day_number', $today->dayOfWeek)->first();
        $hasTemplate = $workingDay && $workingDay->timeSlots()->count() > 0;
        $isImported = $calendarDay && $calendarDay->tasks()->count() > 0;

        $tasks = $calendarDay ? $calendarDay->tasks()->orderBy('sort_order')->orderBy('start_time')->get() : collect();
        $completedCount = $tasks->where('is_completed', true)->count();
        $totalCount = $tasks->count();
        $pct = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        return view('admin.today', compact(
            'today', 'calendarDay', 'workingDay', 'hasTemplate', 'isImported',
            'tasks', 'completedCount', 'totalCount', 'pct'
        ));
    }

    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get calendar days that have tasks for this month
        $calendarDays = CalendarDay::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->withCount(['tasks', 'completedTasks'])
            ->get()
            ->keyBy(fn ($day) => $day->date->format('Y-m-d'));

        return view('admin.scheduler.calendar', compact(
            'startOfMonth', 'endOfMonth', 'calendarDays', 'month', 'year'
        ));
    }

    public function day(Request $request, string $date)
    {
        $date = Carbon::parse($date);
        $calendarDay = CalendarDay::where('date', $date->toDateString())->first();

        $workingDay = WorkingDay::where('day_number', $date->dayOfWeek)->first();
        $hasTemplate = $workingDay && $workingDay->timeSlots()->count() > 0;
        $isImported = $calendarDay && $calendarDay->tasks()->count() > 0;

        return view('admin.scheduler.calendar-day', compact(
            'date', 'calendarDay', 'workingDay', 'hasTemplate', 'isImported'
        ));
    }

    public function importSlots(Request $request, string $date)
    {
        $date = Carbon::parse($date);
        $workingDay = WorkingDay::where('day_number', $date->dayOfWeek)->first();

        if (!$workingDay) {
            return back()->with('error', 'No working day template found.');
        }

        $calendarDay = CalendarDay::firstOrCreate(
            ['date' => $date->toDateString()],
        );

        // Don't re-import if tasks already exist
        if ($calendarDay->tasks()->count() > 0) {
            return back()->with('error', 'Tasks already imported for this day. Clear them first to re-import.');
        }

        foreach ($workingDay->timeSlots as $slot) {
            $calendarDay->tasks()->create([
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'description' => $slot->description,
                'pillar' => $slot->pillar,
                'sort_order' => $slot->sort_order,
                'source_time_slot_id' => $slot->id,
            ]);
        }

        return back()->with('success', 'Imported ' . $workingDay->timeSlots->count() . ' time slots from ' . $workingDay->day_name . ' template.');
    }

    public function toggleTask(CalendarTask $calendarTask)
    {
        $calendarTask->update(['is_completed' => !$calendarTask->is_completed]);

        return back()->with('success', $calendarTask->is_completed ? 'Task completed!' : 'Task unchecked.');
    }

    public function updateTask(Request $request, CalendarTask $calendarTask)
    {
        $validated = $request->validate([
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['required', 'string', 'max:255'],
            'pillar' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $calendarTask->update($validated);

        return back()->with('success', 'Task updated.');
    }

    public function addTask(Request $request, string $date)
    {
        $date = Carbon::parse($date);
        $calendarDay = CalendarDay::firstOrCreate(['date' => $date->toDateString()]);

        $validated = $request->validate([
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['required', 'string', 'max:255'],
            'pillar' => ['required', 'string', 'max:255'],
        ]);

        $validated['sort_order'] = $calendarDay->tasks()->count();
        $calendarDay->tasks()->create($validated);

        return back()->with('success', 'Task added.');
    }

    public function destroyTask(CalendarTask $calendarTask)
    {
        $calendarTask->delete();
        return back()->with('success', 'Task removed.');
    }

    public function clearDay(string $date)
    {
        $date = Carbon::parse($date);
        $calendarDay = CalendarDay::where('date', $date->toDateString())->first();

        if ($calendarDay) {
            $calendarDay->tasks()->delete();
            $calendarDay->delete();
        }

        return back()->with('success', 'Day cleared. You can re-import the template.');
    }

    public function updateDayNotes(Request $request, string $date)
    {
        $date = Carbon::parse($date);
        $calendarDay = CalendarDay::firstOrCreate(['date' => $date->toDateString()]);

        $calendarDay->update(['notes' => $request->input('notes')]);

        return back()->with('success', 'Notes saved.');
    }
}
