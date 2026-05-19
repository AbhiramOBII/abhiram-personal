@extends('admin.layouts.app')

@section('title', 'Monthly Report — ' . $month->format('F Y'))

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="max-w-[900px] mx-auto pb-32" x-data x-init="initMonthlyCharts()">

    {{-- Header with navigation --}}
    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('admin.analytics.monthly', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 no-underline hover:bg-slate-50 transition-all">← {{ $month->copy()->subMonth()->format('M') }}</a>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $month->format('F Y') }}</h1>
        @if($month->copy()->addMonth()->lte(now()))
            <a href="{{ route('admin.analytics.monthly', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 no-underline hover:bg-slate-50 transition-all">{{ $month->copy()->addMonth()->format('M') }} →</a>
        @else
            <span class="px-3 py-2 text-sm text-slate-300">Current</span>
        @endif
    </div>

    {{-- KPI Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-slate-800 tracking-tight">{{ $snapshot['completion_rate'] }}%</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Completion</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600 tracking-tight">{{ $snapshot['tasks_completed'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Tasks Done</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-purple-600 tracking-tight">{{ $snapshot['practices_rate'] }}%</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Practices</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-violet-600 tracking-tight">{{ $snapshot['upskill_hours'] }}h</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Upskill</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-teal-600 tracking-tight">{{ $snapshot['avg_identity_score'] ?? '—' }}</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Identity</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-amber-600 tracking-tight">{{ $snapshot['avg_energy_rating'] ?? '—' }}</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Energy</p>
        </div>
    </div>

    {{-- Pillar Doughnut + Top Learning --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Pillar Breakdown</h3>
            <canvas id="monthlyPillarChart" height="220"></canvas>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Top Learning Items</h3>
            @if(!empty($topLearning))
                <div class="space-y-3 mt-4">
                    @foreach($topLearning as $item)
                        <div class="flex items-center justify-between gap-3 py-2 border-b border-slate-50 last:border-b-0">
                            <span class="text-sm font-medium text-slate-700 truncate">{{ $item['title'] }}</span>
                            <span class="text-xs font-bold text-purple-600 shrink-0">{{ $item['hours'] }}h</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400 text-center mt-8">No learning sessions this month</p>
            @endif
        </div>
    </div>

    {{-- Week-by-Week Breakdown --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-3 px-1">Week by Week</h2>
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Week</th>
                            <th class="text-center px-3 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Tasks</th>
                            <th class="text-center px-3 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Practices</th>
                            <th class="text-center px-3 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Identity</th>
                            <th class="text-center px-3 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Energy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($snapshot['reviews'] as $review)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                                <td class="px-4 py-3 text-sm font-medium text-slate-700">
                                    {{ $review->week_start->format('M j') }} – {{ $review->week_end->format('M j') }}
                                </td>
                                <td class="text-center px-3 py-3 text-slate-600">
                                    @php $ws = app(\App\Services\WeeklyReviewService::class)->getWeekStats($review); @endphp
                                    {{ $ws['tasks_completed'] }}/{{ $ws['tasks_planned'] }}
                                </td>
                                <td class="text-center px-3 py-3 text-slate-600">{{ $ws['practices_rate'] }}%</td>
                                <td class="text-center px-3 py-3">
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 text-purple-600">{{ $review->identity_score ?? '—' }}/10</span>
                                </td>
                                <td class="text-center px-3 py-3">
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-600">{{ $review->energy_rating ?? '—' }}/10</span>
                                </td>
                            </tr>
                        @endforeach
                        @if($snapshot['reviews']->isEmpty())
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">No completed reviews for this month</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Identity Narratives --}}
    @if($snapshot['reviews']->whereNotNull('identity_note')->count())
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-3 px-1">Identity Journal</h2>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 space-y-4">
            @foreach($snapshot['reviews']->whereNotNull('identity_note') as $review)
                <div class="border-l-4 border-purple-200 pl-4">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Week of {{ $review->week_start->format('M j') }}</p>
                    <p class="text-sm text-slate-600 italic leading-relaxed">{{ $review->identity_note }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Back link --}}
    <div class="text-center">
        <a href="{{ route('admin.analytics.index') }}" class="text-sm font-medium text-slate-500 no-underline hover:text-slate-700 transition-colors">← Back to Analytics</a>
    </div>
</div>

@push('scripts')
<script>
function initMonthlyCharts() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#64748b';

    const pillarData = @js($pillarData);
    const pillarColors = { revenue: '#f59e0b', marketing: '#f97316', creation: '#8b5cf6', networking: '#3b82f6', learning: '#a855f7', recovery: '#10b981', untagged: '#94a3b8' };
    const labels = Object.keys(pillarData).map(l => l.charAt(0).toUpperCase() + l.slice(1));
    const values = Object.values(pillarData).map(p => p.completed);
    const colors = Object.keys(pillarData).map(l => pillarColors[l] || '#94a3b8');

    new Chart(document.getElementById('monthlyPillarChart'), {
        type: 'doughnut',
        data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: { plugins: { legend: { position: 'bottom', labels: { padding: 12 } } } }
    });
}
</script>
@endpush
@endsection
