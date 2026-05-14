<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use App\Models\WorkingDay;
use Illuminate\Http\Request;

class TimeSlotsController extends Controller
{
    public function index(WorkingDay $workingDay)
    {
        $workingDay->load('timeSlots');
        $days = WorkingDay::orderBy('day_number')->get();

        return view('admin.scheduler.time-slots', compact('workingDay', 'days'));
    }

    public function store(Request $request, WorkingDay $workingDay)
    {
        $validated = $request->validate([
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['required', 'string', 'max:255'],
            'pillar' => ['required', 'string', 'max:255'],
        ]);

        $validated['sort_order'] = $workingDay->timeSlots()->count();

        $workingDay->timeSlots()->create($validated);

        return back()->with('success', 'Time slot added.');
    }

    public function update(Request $request, TimeSlot $timeSlot)
    {
        $validated = $request->validate([
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['required', 'string', 'max:255'],
            'pillar' => ['required', 'string', 'max:255'],
        ]);

        $timeSlot->update($validated);

        return back()->with('success', 'Time slot updated.');
    }

    public function destroy(TimeSlot $timeSlot)
    {
        $workingDayId = $timeSlot->working_day_id;
        $timeSlot->delete();

        return back()->with('success', 'Time slot removed.');
    }
}
