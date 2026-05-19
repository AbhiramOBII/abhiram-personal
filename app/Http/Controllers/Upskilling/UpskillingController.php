<?php

namespace App\Http\Controllers\Upskilling;

use App\Http\Controllers\Controller;
use App\Models\LearningItem;
use App\Models\LearningSession;
use App\Models\SkillDomain;
use App\Services\UpskillingService;
use Illuminate\Http\Request;

class UpskillingController extends Controller
{
    public function __construct(protected UpskillingService $service) {}

    public function index()
    {
        $domains = SkillDomain::active()
            ->ordered()
            ->withCount(['learningItems as pending_items_count' => fn($q) => $q->where('is_completed', false)->where('is_active', true)])
            ->get();

        $suggestion = $this->service->getTodaySuggestion();
        $todayMinutes = $this->service->getTodayMinutes();
        $weekMinutes = $this->service->getWeekMinutes();
        $monthMinutes = $this->service->getMonthMinutes();
        $totalMinutes = $this->service->getTotalMinutes();

        $pendingItems = LearningItem::active()
            ->pending()
            ->with('skillDomain')
            ->orderByDesc('priority')
            ->get();

        $recentSessions = LearningSession::with(['learningItem', 'skillDomain'])
            ->orderByDesc('session_date')
            ->orderByDesc('started_at')
            ->limit(10)
            ->get();

        $allDomains = SkillDomain::active()->ordered()->get();

        return view('upskilling.index', compact(
            'domains',
            'suggestion',
            'todayMinutes',
            'weekMinutes',
            'monthMinutes',
            'totalMinutes',
            'pendingItems',
            'recentSessions',
            'allDomains',
        ));
    }

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'skill_domain_id' => 'required|exists:skill_domains,id',
            'title' => 'required|string|max:255',
            'type' => 'sometimes|in:course,book,video,article,podcast,experiment',
            'source_url' => 'nullable|string|max:500',
            'estimated_hours' => 'nullable|numeric|min:0',
            'priority' => 'sometimes|integer|min:1|max:10',
            'notes' => 'nullable|string',
        ]);

        $validated['sort_order'] = LearningItem::where('skill_domain_id', $validated['skill_domain_id'])->max('sort_order') + 1;

        LearningItem::create($validated);

        return redirect()->back()->with('success', 'Learning item added.');
    }

    public function updateItem(Request $request, LearningItem $item)
    {
        $validated = $request->validate([
            'skill_domain_id' => 'sometimes|exists:skill_domains,id',
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:course,book,video,article,podcast,experiment',
            'source_url' => 'nullable|string|max:500',
            'estimated_hours' => 'nullable|numeric|min:0',
            'priority' => 'sometimes|integer|min:1|max:10',
            'notes' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->back()->with('success', 'Learning item updated.');
    }

    public function completeItem(LearningItem $item)
    {
        $item->complete();

        return redirect()->back()->with('success', 'Item marked as complete!');
    }

    public function storeDomain(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_emoji' => 'nullable|string|max:10',
            'hex_color' => 'nullable|string|max:7',
            'current_level' => 'sometimes|integer|min:1|max:10',
            'target_level' => 'sometimes|integer|min:1|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['sort_order'] = SkillDomain::max('sort_order') + 1;

        SkillDomain::create($validated);

        return redirect()->back()->with('success', 'Skill domain created.');
    }

    public function updateDomain(Request $request, SkillDomain $domain)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'icon_emoji' => 'nullable|string|max:10',
            'hex_color' => 'nullable|string|max:7',
            'current_level' => 'sometimes|integer|min:1|max:10',
            'target_level' => 'sometimes|integer|min:1|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        $domain->update($validated);

        return redirect()->back()->with('success', 'Skill domain updated.');
    }
}
