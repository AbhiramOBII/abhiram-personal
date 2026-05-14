<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkingDay;
use Illuminate\Http\Request;

class WorkingDaysController extends Controller
{
    public function index()
    {
        $days = WorkingDay::orderBy('day_number')->get();
        return view('admin.scheduler.working-days', compact('days'));
    }

    public function update(Request $request, WorkingDay $workingDay)
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:7'],
            'is_active' => ['boolean'],
        ]);

        $workingDay->update($validated);

        return back()->with('success', 'Working day updated successfully.');
    }
}
