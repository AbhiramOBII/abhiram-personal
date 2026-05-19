<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeBlock;
use App\Models\WorkingDay;
use Illuminate\Http\Request;

class TimeBlocksController extends Controller
{
    public function overview()
    {
        $days = WorkingDay::ordered()->with(['timeBlocks' => fn($q) => $q->orderBy('sort_order')->orderBy('start_time')])->get();

        return view('admin.settings.working-hours.index', compact('days'));
    }

    public function index(WorkingDay $workingDay)
    {
        $blocks = $workingDay->timeBlocks()->orderBy('sort_order')->orderBy('start_time')->get();

        return view('admin.settings.time-blocks.index', compact('workingDay', 'blocks'));
    }

    public function edit(TimeBlock $timeBlock)
    {
        $timeBlock->load('workingDay');

        return view('admin.settings.time-blocks.edit', compact('timeBlock'));
    }

    public function update(Request $request, TimeBlock $timeBlock)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:60',
            'block_type' => 'required|in:work,break,free,recovery',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'intent'     => 'nullable|string|max:200',
            'capacity'   => 'required|integer|min:0|max:10',
            'is_active'  => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $timeBlock->update($validated);

        return redirect()
            ->route('admin.settings.time-blocks.index', $timeBlock->working_day_id)
            ->with('success', "\"{$timeBlock->name}\" updated.");
    }

    public function toggleActive(TimeBlock $timeBlock)
    {
        $timeBlock->update(['is_active' => !$timeBlock->is_active]);

        return back()->with('success', "\"{$timeBlock->name}\" " . ($timeBlock->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function reorder(Request $request, WorkingDay $workingDay)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->input('ids') as $index => $id) {
            TimeBlock::where('id', $id)->where('working_day_id', $workingDay->id)->update(['sort_order' => $index]);
        }

        return response()->json(['ok' => true]);
    }
}
