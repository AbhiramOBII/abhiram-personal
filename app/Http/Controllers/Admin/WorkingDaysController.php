<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkingDay;
use Illuminate\Http\Request;

class WorkingDaysController extends Controller
{
    public function index()
    {
        $days = WorkingDay::ordered()->get();

        return view('admin.settings.working-days.index', compact('days'));
    }

    public function edit(WorkingDay $workingDay)
    {
        return view('admin.settings.working-days.edit', compact('workingDay'));
    }

    public function update(Request $request, WorkingDay $workingDay)
    {
        $validated = $request->validate([
            'theme'                => 'required|string|max:100',
            'theme_short'          => 'nullable|string|max:40',
            'hex_color'            => 'required|string|max:7',
            'icon_emoji'           => 'required|string|max:10',
            'description'          => 'nullable|string|max:500',
            'energy_profile'       => 'required|in:low,medium,high,creative,social',
            'pillars'              => 'nullable|array',
            'pillars.*'            => 'string|max:40',
            'upskill_focus'        => 'nullable|string|max:120',
            'is_active'            => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $workingDay->update($validated);

        return redirect()
            ->route('admin.settings.working-days.index')
            ->with('success', "{$workingDay->day_name} updated.");
    }

    public function toggleActive(WorkingDay $workingDay)
    {
        $workingDay->update(['is_active' => !$workingDay->is_active]);

        return back()->with('success', "{$workingDay->day_name} " . ($workingDay->is_active ? 'enabled' : 'disabled') . '.');
    }
}
