<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\AIOutput;
use App\Services\AIService;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(protected AnalyticsService $service) {}

    public function index(AIService $aiService)
    {
        $from = now()->subDays(30);
        $to = now();

        $chartData = $this->buildPayload($from, $to);
        $aiPattern = $aiService->getPatternInsight();
        $aiPatternDate = AIOutput::where('feature', 'pattern_insight')->latest('context_date')->value('context_date');

        return view('analytics.index', compact('chartData', 'from', 'to', 'aiPattern', 'aiPatternDate'));
    }

    public function data(Request $request): JsonResponse
    {
        $range = $request->get('range', '30d');

        if ($range === 'custom') {
            $from = Carbon::parse($request->get('from', now()->subDays(30)->toDateString()));
            $to = Carbon::parse($request->get('to', now()->toDateString()));
        } else {
            $days = match ($range) {
                '7d' => 7,
                '90d' => 90,
                default => 30,
            };
            $from = now()->subDays($days);
            $to = now();
        }

        return response()->json($this->buildPayload($from, $to));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month')
            ? Carbon::createFromFormat('Y-m', $request->get('month'))
            : now();

        $snapshot = $this->service->monthlySnapshot($month);
        $pillarData = $this->service->taskCompletionByPillar($month->copy()->startOfMonth(), $month->copy()->endOfMonth());

        $topLearning = \App\Models\LearningSession::whereBetween('session_date', [
            $month->copy()->startOfMonth()->toDateString(),
            $month->copy()->endOfMonth()->toDateString(),
        ])
            ->whereNotNull('learning_item_id')
            ->selectRaw('learning_item_id, sum(duration_minutes) as total_min')
            ->groupBy('learning_item_id')
            ->orderByDesc('total_min')
            ->limit(3)
            ->get()
            ->map(function ($row) {
                $item = \App\Models\LearningItem::find($row->learning_item_id);
                return [
                    'title' => $item?->title ?? 'Unknown',
                    'hours' => round($row->total_min / 60, 1),
                ];
            })->toArray();

        return view('analytics.monthly', compact('snapshot', 'pillarData', 'topLearning', 'month'));
    }

    protected function buildPayload(Carbon $from, Carbon $to): array
    {
        $weekdays = $this->service->taskCompletionByWeekday($from, $to);
        $pillars = $this->service->taskCompletionByPillar($from, $to);
        $heatmap = $this->service->dailyCompletionHeatmap($from, $to);
        $peakHours = $this->service->peakProductivityWindows($from, $to);
        $rollover = $this->service->rolloverTrend($from, $to);
        $practices = $this->service->practiceConsistency($from, $to);
        $upskilling = $this->service->upskillingTrend($from, $to);
        $reviewTrend = $this->service->weeklyReviewTrend();
        $identityTrend = $this->service->identityScoreTrend();

        // KPI calculations
        $totalPlanned = collect($weekdays)->sum('planned');
        $totalCompleted = collect($weekdays)->sum('completed');
        $completionRate = $totalPlanned > 0 ? (int) round(($totalCompleted / $totalPlanned) * 100) : 0;

        $practiceRate = count($practices) > 0
            ? (int) round(collect($practices)->avg('rate'))
            : 0;

        $upskillHours = round(collect($upskilling['weekly'])->sum('total_minutes') / 60, 1);

        $avgIdentity = count($identityTrend['identity_scores']) > 0
            ? round(collect($identityTrend['identity_scores'])->filter()->avg(), 1)
            : null;
        $avgEnergy = count($identityTrend['energy_ratings']) > 0
            ? round(collect($identityTrend['energy_ratings'])->filter()->avg(), 1)
            : null;

        // Peak hours top 3
        arsort($peakHours);
        $top3Peak = array_slice(array_keys($peakHours), 0, 3, true);

        // Working day colors
        $dayColors = \App\Models\WorkingDay::all()->pluck('hex_color', 'day_number')->toArray();

        return [
            'kpis' => [
                'completion_rate' => $completionRate,
                'tasks_completed' => $totalCompleted,
                'practice_rate' => $practiceRate,
                'upskill_hours' => $upskillHours,
                'avg_identity' => $avgIdentity,
                'avg_energy' => $avgEnergy,
            ],
            'weekdays' => $weekdays,
            'pillars' => $pillars,
            'heatmap' => $heatmap,
            'peak_hours' => $peakHours,
            'top3_peak' => array_values($top3Peak),
            'rollover' => $rollover,
            'practices' => $practices,
            'upskilling' => $upskilling,
            'review_trend' => $reviewTrend,
            'identity_trend' => $identityTrend,
            'day_colors' => $dayColors,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ];
    }
}
