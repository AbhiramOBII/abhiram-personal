@extends('admin.layouts.app')

@section('title', 'Analytics & Insights')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section('content')
<div class="max-w-[1100px] mx-auto pb-32" x-data="analyticsPage()" x-init="initCharts()">

    {{-- Header + Date Range Filter --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Analytics & Insights</h1>
        <div class="flex flex-wrap gap-2">
            <button @click="setRange('7d')" :class="activeRange === '7d' ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer">7 Days</button>
            <button @click="setRange('30d')" :class="activeRange === '30d' ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer">30 Days</button>
            <button @click="setRange('90d')" :class="activeRange === '90d' ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer">90 Days</button>
            <div class="flex items-center gap-1.5">
                <input type="date" x-model="customFrom" class="px-2.5 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 outline-none">
                <span class="text-slate-400 text-xs">→</span>
                <input type="date" x-model="customTo" class="px-2.5 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 outline-none">
                <button @click="setRange('custom')" class="px-3 py-2 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 transition-all cursor-pointer">Go</button>
            </div>
        </div>
    </div>

    {{-- AI Pattern Insight --}}
    @if(!empty($aiPattern))
    <div class="mb-8 rounded-2xl bg-slate-50/70 p-4">
        <div class="flex items-start gap-3">
            <span class="text-lg leading-none mt-0.5">💡</span>
            <div class="flex-1">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold mb-1.5">Pattern Insight</p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $aiPattern }}</p>
                @if($aiPatternDate)
                <p class="text-[10px] text-slate-400 mt-2">Last updated {{ $aiPatternDate->diffForHumans() }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Row 1 — KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-slate-800 tracking-tight" x-text="data.kpis.completion_rate + '%'"></p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Completion Rate</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600 tracking-tight" x-text="data.kpis.tasks_completed"></p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Tasks Done</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-purple-600 tracking-tight" x-text="data.kpis.practice_rate + '%'"></p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Practice Rate</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-violet-600 tracking-tight" x-text="data.kpis.upskill_hours + 'h'"></p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Upskill Hours</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-teal-600 tracking-tight" x-text="data.kpis.avg_identity ?? '—'"></p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Avg Identity</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-amber-600 tracking-tight" x-text="data.kpis.avg_energy ?? '—'"></p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Avg Energy</p>
        </div>
    </div>

    {{-- Row 2 — Activity Heatmap --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-3 px-1">Activity Heatmap</h2>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 overflow-x-auto">
            <div x-ref="heatmapContainer" x-html="renderHeatmap()"></div>
        </div>
    </div>

    {{-- Row 3 — Weekday + Pillar Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Completion by Weekday</h3>
            <canvas x-ref="weekdayChart" height="200"></canvas>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Pillar Balance</h3>
            <canvas x-ref="pillarChart" height="200"></canvas>
        </div>
    </div>

    {{-- Row 4 — Peak Productivity Windows --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-3 px-1">Peak Productivity</h2>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <p class="text-xs text-slate-500 mb-3" x-show="data.top3_peak.length > 0">
                <span class="font-semibold text-amber-600">⚡ Peak:</span>
                <span x-text="data.top3_peak.map(h => formatHour(h)).join(', ')"></span>
            </p>
            <canvas x-ref="peakChart" height="100"></canvas>
            <p class="text-xs text-slate-400 mt-3" x-show="data.top3_peak.length > 0">
                Your most productive window is <span class="font-medium text-slate-600" x-text="formatHour(data.top3_peak[0]) + ' – ' + formatHour((data.top3_peak[0] || 0) + 1)"></span>. Schedule deep work here.
            </p>
        </div>
    </div>

    {{-- Row 5 — Rollover + Upskilling Trend --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Rollover Trend</h3>
            <canvas x-ref="rolloverChart" height="200"></canvas>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Upskilling Trend</h3>
            <canvas x-ref="upskillingChart" height="200"></canvas>
        </div>
    </div>

    {{-- Row 6 — Practice Consistency Table --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-3 px-1">Practice Consistency</h2>
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Practice</th>
                            <th class="text-center px-3 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Done/Possible</th>
                            <th class="text-center px-3 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Rate</th>
                            <th class="text-center px-3 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Streak 🔥</th>
                            <th class="text-center px-3 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Longest</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="p in data.practices" :key="p.id">
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span x-text="p.emoji" class="text-lg"></span>
                                        <span class="font-medium text-slate-700 text-sm" x-text="p.name"></span>
                                    </div>
                                </td>
                                <td class="text-center px-3 py-3 text-slate-600" x-text="p.completed_days + '/' + p.possible_days"></td>
                                <td class="text-center px-3 py-3">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-bold"
                                        :class="p.rate >= 80 ? 'bg-green-100 text-green-700' : (p.rate >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')"
                                        x-text="p.rate + '%'"></span>
                                </td>
                                <td class="text-center px-3 py-3 font-semibold text-slate-700" x-text="p.current_streak"></td>
                                <td class="text-center px-3 py-3 text-slate-500" x-text="p.longest_streak"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Row 7 — Identity & Energy Trend --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-3 px-1">Identity & Energy Trend</h2>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <canvas x-ref="identityChart" height="180"></canvas>
        </div>
    </div>

    {{-- Row 8 — Monthly Snapshots --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-3 px-1">Monthly Snapshots</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @for($i = 0; $i < 3; $i++)
                @php $m = now()->subMonths($i); @endphp
                <a href="{{ route('admin.analytics.monthly', ['month' => $m->format('Y-m')]) }}" class="bg-white border border-slate-200 rounded-xl p-4 hover:shadow-md transition-shadow no-underline">
                    <p class="text-sm font-bold text-slate-800">{{ $m->format('F Y') }}</p>
                    <p class="text-xs text-slate-400 mt-1">Click to view full report →</p>
                </a>
            @endfor
        </div>
    </div>
</div>

@push('scripts')
<script>
function analyticsPage() {
    return {
        data: @js($chartData),
        activeRange: '30d',
        customFrom: '{{ $from->toDateString() }}',
        customTo: '{{ $to->toDateString() }}',
        charts: {},

        async setRange(range) {
            this.activeRange = range;
            let url = '{{ route("admin.api.analytics.data") }}?range=' + range;
            if (range === 'custom') {
                url += '&from=' + this.customFrom + '&to=' + this.customTo;
            }
            const res = await fetch(url, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            this.data = await res.json();
            this.updateCharts();
        },

        formatHour(h) {
            if (h === 0) return '12 AM';
            if (h < 12) return h + ' AM';
            if (h === 12) return '12 PM';
            return (h - 12) + ' PM';
        },

        renderHeatmap() {
            const heatmap = this.data.heatmap;
            const dayColors = this.data.day_colors;
            const dates = Object.keys(heatmap).sort();
            if (dates.length === 0) return '<p class="text-xs text-slate-400 text-center">No data yet</p>';

            const cellSize = 14;
            const gap = 2;
            const start = new Date(dates[0]);
            const end = new Date(dates[dates.length - 1]);
            const weeks = Math.ceil((end - start) / (7 * 86400000)) + 1;
            const width = weeks * (cellSize + gap) + 40;
            const height = 7 * (cellSize + gap) + 20;
            const dayLabels = ['M','T','W','T','F','S','S'];

            let svg = `<svg width="${width}" height="${height}" class="block">`;
            for (let row = 0; row < 7; row++) {
                svg += `<text x="0" y="${row * (cellSize + gap) + cellSize + 10}" class="fill-slate-400" style="font-size:9px">${dayLabels[row]}</text>`;
            }

            dates.forEach(dateStr => {
                const d = new Date(dateStr);
                const dayOfWeek = (d.getDay() + 6) % 7; // Mon=0
                const weekNum = Math.floor((d - start) / (7 * 86400000));
                const x = 25 + weekNum * (cellSize + gap);
                const y = dayOfWeek * (cellSize + gap) + 10;
                const pct = heatmap[dateStr] || 0;
                const dayNum = d.getDay();
                const baseColor = dayColors[dayNum] || '#94a3b8';
                const opacity = Math.max(0.1, pct / 100);
                const fill = pct === 0 ? '#f1f5f9' : baseColor;
                const fillOpacity = pct === 0 ? 1 : opacity;
                svg += `<rect x="${x}" y="${y}" width="${cellSize}" height="${cellSize}" rx="3" fill="${fill}" opacity="${fillOpacity}"><title>${dateStr}: ${pct}%</title></rect>`;
            });

            svg += '</svg>';
            return svg;
        },

        initCharts() {
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.font.size = 11;
            Chart.defaults.color = '#64748b';
            Chart.defaults.plugins.legend.display = false;
            Chart.defaults.scale.grid = { color: '#f1f5f9' };

            this.createWeekdayChart();
            this.createPillarChart();
            this.createPeakChart();
            this.createRolloverChart();
            this.createUpskillingChart();
            this.createIdentityChart();
        },

        updateCharts() {
            Object.values(this.charts).forEach(c => c.destroy());
            this.charts = {};
            this.initCharts();
        },

        createWeekdayChart() {
            const dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            const dayColors = this.data.day_colors;
            const labels = [];
            const rates = [];
            const colors = [];

            for (let d = 1; d <= 6; d++) {
                labels.push(dayNames[d]);
                rates.push(this.data.weekdays[d]?.rate || 0);
                colors.push(dayColors[d] || '#94a3b8');
            }
            labels.push(dayNames[0]);
            rates.push(this.data.weekdays[0]?.rate || 0);
            colors.push(dayColors[0] || '#94a3b8');

            this.charts.weekday = new Chart(this.$refs.weekdayChart, {
                type: 'bar',
                data: { labels, datasets: [{ data: rates, backgroundColor: colors, borderRadius: 6 }] },
                options: { scales: { y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } }, plugins: { tooltip: { callbacks: { label: ctx => {
                    const idx = ctx.dataIndex;
                    const dNum = idx < 6 ? idx + 1 : 0;
                    const w = this.data.weekdays[dNum];
                    return `${w.completed}/${w.planned} tasks (${w.rate}%)`;
                }}}}}
            });
        },

        createPillarChart() {
            const pillars = this.data.pillars;
            const pillarColors = { revenue: '#f59e0b', marketing: '#f97316', creation: '#8b5cf6', networking: '#3b82f6', learning: '#a855f7', recovery: '#10b981', untagged: '#94a3b8' };
            const labels = Object.keys(pillars);
            const values = labels.map(l => pillars[l].completed);
            const colors = labels.map(l => pillarColors[l] || '#94a3b8');

            this.charts.pillar = new Chart(this.$refs.pillarChart, {
                type: 'bar',
                data: { labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)), datasets: [{ data: values, backgroundColor: colors, borderRadius: 6 }] },
                options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
            });
        },

        createPeakChart() {
            const hours = this.data.peak_hours;
            const labels = Object.keys(hours).map(h => this.formatHour(parseInt(h)));
            const values = Object.values(hours);
            const max = Math.max(...values, 1);
            const colors = values.map(v => {
                const intensity = Math.max(0.15, v / max);
                return `rgba(245, 158, 11, ${intensity})`;
            });

            this.charts.peak = new Chart(this.$refs.peakChart, {
                type: 'bar',
                data: { labels, datasets: [{ data: values, backgroundColor: colors, borderRadius: 4 }] },
                options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
            });
        },

        createRolloverChart() {
            const rollover = this.data.rollover;
            const labels = Object.keys(rollover);
            const rolledData = labels.map(l => rollover[l].rolled_over_count);
            const completedData = labels.map(l => rollover[l].completed_count);

            this.charts.rollover = new Chart(this.$refs.rolloverChart, {
                type: 'line',
                data: {
                    labels: labels.map(l => { const d = new Date(l); return d.toLocaleDateString('en', { month: 'short', day: 'numeric' }); }),
                    datasets: [
                        { label: 'Rolled Over', data: rolledData, borderColor: '#f59e0b', backgroundColor: '#f59e0b20', tension: 0.3, fill: true },
                        { label: 'Completed', data: completedData, borderColor: '#14b8a6', backgroundColor: '#14b8a620', tension: 0.3, fill: true },
                    ]
                },
                options: { plugins: { legend: { display: true, position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
            });
        },

        createUpskillingChart() {
            const weekly = this.data.upskilling.weekly;
            const labels = Object.keys(weekly);
            const values = labels.map(l => weekly[l].total_minutes);

            this.charts.upskilling = new Chart(this.$refs.upskillingChart, {
                type: 'bar',
                data: {
                    labels: labels.map(l => { const d = new Date(l); return d.toLocaleDateString('en', { month: 'short', day: 'numeric' }); }),
                    datasets: [{ label: 'Minutes', data: values, backgroundColor: '#a855f7', borderRadius: 6 }]
                },
                options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
            });
        },

        createIdentityChart() {
            const trend = this.data.identity_trend;
            if (!trend.labels.length) return;

            this.charts.identity = new Chart(this.$refs.identityChart, {
                type: 'line',
                data: {
                    labels: trend.labels,
                    datasets: [
                        { label: 'Identity Score', data: trend.identity_scores, borderColor: '#8b5cf6', backgroundColor: '#8b5cf620', tension: 0.3, fill: true },
                        { label: 'Energy Rating', data: trend.energy_ratings, borderColor: '#14b8a6', backgroundColor: '#14b8a620', tension: 0.3, fill: true },
                    ]
                },
                options: { scales: { y: { min: 0, max: 10 } }, plugins: { legend: { display: true, position: 'bottom' } } }
            });
        }
    };
}
</script>
@endpush
@endsection
