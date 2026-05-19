<?php

namespace App\Http\Controllers\PracticeManagement;

use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Services\PracticeService;
use Illuminate\Http\Request;

class PracticeController extends Controller
{
    public function __construct(protected PracticeService $service) {}

    public function index()
    {
        $practices = Practice::active()
            ->with(['stackAfter', 'stackedPractices'])
            ->orderBy('sort_order')
            ->get();

        $today = now()->toDateString();
        $thirtyDaysAgo = now()->subDays(30)->toDateString();

        $todayLogs = PracticeLog::where('logged_date', $today)
            ->get()
            ->keyBy('practice_id');

        $heatmapData = [];
        $streakData = [];

        foreach ($practices as $practice) {
            $streakData[$practice->id] = $this->service->getStreakData($practice);

            $logs = $practice->logs()
                ->where('logged_date', '>=', $thirtyDaysAgo)
                ->where('logged_date', '<=', $today)
                ->where('is_completed', true)
                ->pluck('logged_date')
                ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
                ->toArray();

            $heatmapData[$practice->id] = $logs;
        }

        $allPractices = Practice::active()->orderBy('sort_order')->get();

        $notesData = [];
        foreach ($practices as $practice) {
            $notesData[$practice->id] = $practice->logs()
                ->whereNotNull('note')
                ->where('note', '!=', '')
                ->orderByDesc('logged_date')
                ->get(['id', 'logged_date', 'used_two_minute_version', 'note'])
                ->toArray();
        }

        return view('practice-management.index', compact(
            'practices',
            'todayLogs',
            'streakData',
            'heatmapData',
            'allPractices',
            'notesData'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cue' => 'nullable|string|max:255',
            'reward' => 'nullable|string|max:255',
            'identity_statement' => 'nullable|string|max:255',
            'two_minute_version' => 'nullable|string|max:255',
            'pillar' => 'nullable|string|max:40',
            'hex_color' => 'nullable|string|max:7',
            'icon_emoji' => 'nullable|string|max:10',
            'frequency_type' => 'sometimes|in:daily,specific_days',
            'frequency_days' => 'nullable|array',
            'frequency_days.*' => 'integer|min:0|max:6',
            'stack_after_practice_id' => 'nullable|exists:practices,id',
            'stack_trigger' => 'nullable|string|max:255',
            'is_two_minute_enabled' => 'sometimes|boolean',
        ]);

        $validated['sort_order'] = Practice::max('sort_order') + 1;

        Practice::create($validated);

        $this->service->generateLogsForToday();

        return redirect()->back()->with('success', 'Practice created.');
    }

    public function update(Request $request, Practice $practice)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'cue' => 'nullable|string|max:255',
            'reward' => 'nullable|string|max:255',
            'identity_statement' => 'nullable|string|max:255',
            'two_minute_version' => 'nullable|string|max:255',
            'pillar' => 'nullable|string|max:40',
            'hex_color' => 'nullable|string|max:7',
            'icon_emoji' => 'nullable|string|max:10',
            'frequency_type' => 'sometimes|in:daily,specific_days',
            'frequency_days' => 'nullable|array',
            'frequency_days.*' => 'integer|min:0|max:6',
            'stack_after_practice_id' => 'nullable|exists:practices,id',
            'stack_trigger' => 'nullable|string|max:255',
            'is_two_minute_enabled' => 'sometimes|boolean',
        ]);

        $practice->update($validated);

        return redirect()->back()->with('success', 'Practice updated.');
    }

    public function destroy(Practice $practice)
    {
        $practice->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Practice archived.');
    }
}
