@extends('admin.layouts.app')

@section('title', 'Weekly Review — Week of ' . $review->week_start->format('M j'))

@push('head')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@php
    $sundayHex = '#4f98a3';
    $isSunday = now()->isSunday();
@endphp

@section('content')
<div class="max-w-[860px] mx-auto pb-32" x-data="weeklyReview()">

    {{-- ═══════════════════════════════════════════════
         SECTION 1 — Week Identity Banner
    ═══════════════════════════════════════════════ --}}
    <div class="rounded-2xl p-6 sm:p-8 mb-8 relative overflow-hidden" style="background: {{ $sundayHex }}15; border: 1px solid {{ $sundayHex }}25;">
        <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight">
                    Week of {{ $review->week_start->format('M j') }} → {{ $review->week_end->format('M j') }}
                </h1>
                <p class="text-sm text-slate-500 mt-2 max-w-md">{{ $identityPrompt }}</p>
                @if(!$isSunday)
                    <span class="inline-block mt-3 px-3 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                        Read-only — full editing available on Sunday
                    </span>
                @endif
            </div>
            {{-- Identity Score Circle --}}
            <div class="flex flex-col items-center gap-2 shrink-0">
                <div class="relative w-[100px] h-[100px]">
                    <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                        @for($i = 1; $i <= 10; $i++)
                            <path
                                d="M50 50 L{{ 50 + 40 * cos(deg2rad(($i - 1) * 36 - 90)) }} {{ 50 + 40 * sin(deg2rad(($i - 1) * 36 - 90)) }} A40 40 0 0 1 {{ 50 + 40 * cos(deg2rad($i * 36 - 90)) }} {{ 50 + 40 * sin(deg2rad($i * 36 - 90)) }} Z"
                                class="cursor-pointer transition-all duration-200"
                                :fill="identityScore >= {{ $i }} ? '{{ $sundayHex }}' : '#e2e8f0'"
                                :opacity="identityScore >= {{ $i }} ? '1' : '0.5'"
                                @click="setIdentityScore({{ $i }})"
                                stroke="white" stroke-width="1"
                            />
                        @endfor
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-slate-800" x-text="identityScore || '—'"></span>
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Identity Score</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 2 — Week at a Glance
    ═══════════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4 px-1">Week at a Glance</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold tracking-tight" style="color: {{ $sundayHex }};">{{ $stats['tasks_completed'] }}/{{ $stats['tasks_planned'] }}</p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Tasks ({{ $stats['completion_rate'] }}%)</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-slate-800 tracking-tight">{{ $stats['practices_completed'] }}/{{ $stats['practices_possible'] }}</p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Practices ({{ $stats['practices_rate'] }}%)</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-purple-600 tracking-tight">{{ $stats['upskill_minutes'] }}m</p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Upskilling</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold tracking-tight {{ $stats['project_tasks_overdue'] > 0 ? 'text-red-500' : 'text-blue-600' }}">
                    {{ $stats['project_tasks_completed'] }}/{{ $stats['project_tasks_total'] }}
                    @if($stats['project_tasks_overdue'] > 0)<span class="text-sm text-red-400"> ({{ $stats['project_tasks_overdue'] }} overdue)</span>@endif
                </p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">🗂️ Projects</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                @if($stats['best_day'])
                    <p class="text-lg font-bold text-green-600 tracking-tight">{{ \Carbon\Carbon::parse($stats['best_day'])->format('D') }}</p>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Best Day ({{ $stats['day_completion_rates'][$stats['best_day']] ?? 0 }}%)</p>
                @else
                    <p class="text-lg font-bold text-slate-300">—</p>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Best Day</p>
                @endif
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold tracking-tight {{ $stats['tasks_rolled_over'] > 5 ? 'text-red-500' : 'text-amber-500' }}">
                    {{ $stats['tasks_rolled_over'] }}{{ $stats['tasks_rolled_over'] > 5 ? ' 🔥' : '' }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Rolled Over</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold tracking-tight text-indigo-600">{{ $stats['avg_vs_completed'] ?? 0 }}</p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Avg VS Completed</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold tracking-tight text-amber-500">{{ $stats['resurfaced_count'] ?? 0 }}</p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Resurfaced Tasks</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center gap-1">
                    @for($i = 1; $i <= 10; $i++)
                        <button
                            class="w-2 h-5 rounded-sm transition-all"
                            :class="energyRating >= {{ $i }} ? 'bg-emerald-500' : 'bg-slate-200'"
                            @click="setEnergyRating({{ $i }})"></button>
                    @endfor
                </div>
                <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-widest font-semibold">Energy (<span x-text="energyRating || '—'"></span>/10)</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 3 — Day-by-Day Heatmap
    ═══════════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4 px-1">Day-by-Day</h2>
        <div class="grid grid-cols-7 gap-2">
            @php
                $weekDays = [];
                $d = $review->week_start->copy();
                for ($i = 0; $i < 7; $i++) {
                    $weekDays[] = $d->copy();
                    $d->addDay();
                }
                $workingDays = \App\Models\WorkingDay::all()->keyBy('day_number');
            @endphp
            @foreach($weekDays as $day)
                @php
                    $dateStr = $day->toDateString();
                    $pct = $stats['day_completion_rates'][$dateStr] ?? 0;
                    $wd = $workingDays[$day->dayOfWeek] ?? null;
                    $dayHex = $wd->hex_color ?? '#94a3b8';
                @endphp
                <div class="bg-white border border-slate-200 rounded-xl p-2 text-center flex flex-col items-center gap-1.5">
                    <span class="text-[10px] font-semibold text-slate-500 uppercase">{{ $day->format('D') }}</span>
                    @if($wd)
                        <span class="px-1.5 py-px rounded text-[8px] font-semibold truncate max-w-full" style="background: {{ $dayHex }}15; color: {{ $dayHex }};">{{ Str::limit($wd->day_name, 8) }}</span>
                    @endif
                    <div class="w-4 h-16 bg-slate-100 rounded-full overflow-hidden relative">
                        <div class="absolute bottom-0 left-0 right-0 rounded-full transition-all duration-500" style="height: {{ $pct }}%; background: {{ $dayHex }};"></div>
                    </div>
                    <span class="text-[11px] font-bold text-slate-700">{{ $pct }}%</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 4 — Pillar Balance Wheel
    ═══════════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4 px-1">Pillar Balance</h2>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 flex items-center justify-center">
            @php
                $radarPillars = ['revenue', 'marketing', 'creation', 'networking', 'learning', 'recovery'];
                $maxPillar = !empty($stats['pillar_breakdown']) ? max(array_values($stats['pillar_breakdown'])) : 1;
                $radarColors = ['#f59e0b', '#f97316', '#8b5cf6', '#3b82f6', '#a855f7', '#10b981'];
            @endphp
            <svg viewBox="0 0 300 300" class="w-full max-w-[280px] h-auto">
                {{-- Background rings --}}
                @for($ring = 1; $ring <= 3; $ring++)
                    <polygon
                        points="@foreach($radarPillars as $idx => $p){{ 150 + (($ring/3) * 100) * cos(deg2rad($idx * 60 - 90)) }},{{ 150 + (($ring/3) * 100) * sin(deg2rad($idx * 60 - 90)) }} @endforeach"
                        fill="none" stroke="#e2e8f0" stroke-width="1"/>
                @endfor
                {{-- Axes --}}
                @foreach($radarPillars as $idx => $p)
                    <line x1="150" y1="150"
                          x2="{{ 150 + 100 * cos(deg2rad($idx * 60 - 90)) }}"
                          y2="{{ 150 + 100 * sin(deg2rad($idx * 60 - 90)) }}"
                          stroke="#e2e8f0" stroke-width="1"/>
                @endforeach
                {{-- Data polygon --}}
                @php
                    $polyPoints = '';
                    foreach ($radarPillars as $idx => $p) {
                        $val = ($stats['pillar_breakdown'][$p] ?? 0) / max($maxPillar, 1);
                        $px = 150 + ($val * 100) * cos(deg2rad($idx * 60 - 90));
                        $py = 150 + ($val * 100) * sin(deg2rad($idx * 60 - 90));
                        $polyPoints .= round($px, 2) . ',' . round($py, 2) . ' ';
                    }
                @endphp
                <polygon
                    points="{{ trim($polyPoints) }}"
                    fill="{{ $sundayHex }}30" stroke="{{ $sundayHex }}" stroke-width="2"/>
                {{-- Labels --}}
                @foreach($radarPillars as $idx => $p)
                    @php
                        $lx = 150 + 120 * cos(deg2rad($idx * 60 - 90));
                        $ly = 150 + 120 * sin(deg2rad($idx * 60 - 90));
                        $count = $stats['pillar_breakdown'][$p] ?? 0;
                    @endphp
                    <text x="{{ $lx }}" y="{{ $ly }}" text-anchor="middle" dominant-baseline="middle" class="text-[10px] fill-slate-600 font-semibold">{{ ucfirst($p) }} ({{ $count }})</text>
                @endforeach
            </svg>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 4.5 — AI Weekly Insight
    ═══════════════════════════════════════════════ --}}
    @if(!empty($aiInsight))
    <div class="mb-8 rounded-2xl bg-slate-50 border border-slate-200 p-4" style="border-left: 3px solid #14b8a6;">
        <div class="flex items-start gap-3">
            <span class="text-lg leading-none mt-0.5">🤖</span>
            <div class="flex-1">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold mb-1.5">AI Insight</p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $aiInsight }}</p>
                <form class="mt-2" x-data="{ loading: false }" @submit.prevent="loading = true; fetch('{{ route('admin.api.ai.weekly-insight', $review->id) }}', { method: 'POST', headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'} }).then(r => r.json()).then(d => { $el.closest('div').querySelector('p.text-sm').textContent = d.insight; loading = false; })">
                    <button type="submit" class="text-[10px] font-semibold text-teal-600 hover:text-teal-800 cursor-pointer border-0 bg-transparent" :class="loading && 'opacity-50 pointer-events-none'">
                        <span x-show="!loading">↻ Regenerate</span>
                        <span x-show="loading">Regenerating…</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 5 — Guided Reflection
    ═══════════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4 px-1">Guided Reflection</h2>
        <div class="space-y-4">
            @php
                $reflections = [
                    ['field' => 'reflection_win', 'question' => 'What was your biggest WIN this week?', 'color' => '#10b981'],
                    ['field' => 'reflection_challenge', 'question' => 'What was your biggest CHALLENGE?', 'color' => '#ef4444'],
                    ['field' => 'reflection_learning', 'question' => 'What is the most important thing you LEARNED?', 'color' => '#8b5cf6'],
                    ['field' => 'reflection_gratitude', 'question' => 'What are you GRATEFUL for this week?', 'color' => '#f59e0b'],
                ];
            @endphp
            @foreach($reflections as $r)
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5" style="border-left: 4px solid {{ $r['color'] }};">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">{{ $r['question'] }}</label>
                    <textarea
                        x-model="{{ $r['field'] }}"
                        @blur="saveField('{{ $r['field'] }}', {{ $r['field'] }})"
                        rows="3"
                        class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm text-slate-700 bg-slate-50 resize-y outline-none font-[inherit] transition-colors focus:border-slate-400 placeholder:text-slate-300"
                        placeholder="Take a moment to reflect..."
                        {{ !$isSunday && !$review->{$r['field']} ? 'disabled' : '' }}></textarea>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 6 — Identity Declaration
    ═══════════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4 px-1">Identity Declaration</h2>
        <div class="bg-white border border-slate-200 rounded-xl p-5" style="border-left: 4px solid {{ $sundayHex }};">
            <p class="text-sm text-slate-500 mb-3">{{ $identityPrompt }}</p>
            <textarea
                x-model="identity_note"
                @blur="saveField('identity_note', identity_note)"
                rows="4"
                class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm text-slate-700 bg-slate-50 resize-y outline-none font-[inherit] transition-colors focus:border-slate-400 placeholder:text-slate-300"
                placeholder="I showed up as... I am becoming..."></textarea>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 6b — Reflective Journal
    ═══════════════════════════════════════════════ --}}
    @if(isset($reflectiveLogs) && $reflectiveLogs->count())
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4 px-1">Reflective Journal</h2>
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            @foreach($reflectiveLogs->groupBy(fn($l) => $l->logged_date->format('Y-m-d')) as $date => $dayLogs)
                <div class="border-b border-slate-100 last:border-b-0">
                    <div class="px-5 py-2.5 bg-slate-50/70">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">{{ \Carbon\Carbon::parse($date)->format('l, M j') }}</span>
                    </div>
                    @foreach($dayLogs as $rLog)
                        <div class="px-5 py-3.5 border-t border-slate-50">
                            <div class="flex items-start gap-3">
                                <x-practice-icon :practice="$rLog->practice" size="18" />
                                <div class="flex-1 min-w-0">
                                    <span class="text-[13px] font-medium text-slate-700">{{ $rLog->practice->name }}</span>
                                    @if($rLog->ai_prompt_used)
                                        <p class="text-[11px] text-indigo-500 italic mt-0.5">{{ $rLog->ai_prompt_used }}</p>
                                    @endif
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed whitespace-pre-wrap">{{ $rLog->response_text }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 7 — Next Week Planning
    ═══════════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4 px-1">Next Week Planning</h2>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 space-y-4">
            @for($i = 0; $i < 3; $i++)
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-1.5">Priority {{ $i + 1 }}</label>
                    <input type="text"
                        x-model="priorities[{{ $i }}]"
                        @blur="savePriorities()"
                        class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm text-slate-800 outline-none bg-slate-50 transition-colors focus:border-slate-400 placeholder:text-slate-300"
                        placeholder="What matters most?">
                </div>
            @endfor

            <div class="pt-2 border-t border-slate-100">
                <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-1.5">One sentence that defines next week:</label>
                <input type="text"
                    x-model="next_week_focus"
                    @blur="saveField('next_week_focus', next_week_focus)"
                    class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm text-slate-800 outline-none bg-slate-50 transition-colors focus:border-slate-400 placeholder:text-slate-300"
                    placeholder="Next week I will...">
            </div>

            {{-- Suggestions --}}
            @if(!empty($suggestions))
                <div class="pt-2 border-t border-slate-100">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Suggested Priorities</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($suggestions as $suggestion)
                            <button
                                @click="fillNextPriority('{{ addslashes($suggestion) }}')"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 cursor-pointer transition-all hover:bg-slate-100 hover:border-slate-300">
                                + {{ $suggestion }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 8 — Rollover Offenders
    ═══════════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-4 px-1">Rollover Watch</h2>
        @if(!empty($stats['rollover_offenders']))
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <p class="text-xs font-semibold text-amber-700 mb-3">⚠️ These tasks have been deferred 2+ times this week:</p>
                <div class="space-y-2">
                    @foreach($stats['rollover_offenders'] as $offender)
                        <div class="flex items-center justify-between gap-3 bg-white rounded-lg px-3 py-2 border border-amber-100">
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-slate-700 truncate block">{{ $offender['title'] }}</span>
                                <span class="text-[10px] text-amber-600 font-semibold">↩ {{ $offender['rollover_count'] }}x deferred</span>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <button @click="archiveTask({{ $offender['id'] }})"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white text-slate-500 cursor-pointer hover:bg-slate-50 transition-all">
                                    Archive
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
                <p class="text-sm font-medium text-green-700">Clean slate — no chronic rollovers this week 🎉</p>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 9 — Close the Week
    ═══════════════════════════════════════════════ --}}
    <div class="mb-8" x-show="!isClosed">
        @if($isSunday)
            <div x-show="!showConfirm">
                <button @click="showConfirm = true"
                    class="w-full py-4 rounded-2xl border-0 cursor-pointer text-base font-bold text-white transition-all hover:opacity-90"
                    style="background: {{ $sundayHex }}; box-shadow: 0 4px 16px {{ $sundayHex }}40;">
                    Close This Week →
                </button>
            </div>
            <div x-show="showConfirm" x-cloak class="bg-white border border-slate-200 rounded-2xl p-5 text-center space-y-3">
                <p class="text-sm text-slate-600 font-medium">Once closed, this week is sealed. Ready?</p>
                <div class="flex gap-3 justify-center">
                    <button @click="showConfirm = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-500 cursor-pointer hover:bg-slate-50 transition-all">Cancel</button>
                    <button @click="closeWeek()" :disabled="closing"
                        class="px-5 py-2.5 rounded-xl border-0 text-sm font-bold text-white cursor-pointer transition-all disabled:opacity-50"
                        style="background: {{ $sundayHex }};">
                        Confirm Close
                    </button>
                </div>
            </div>
        @endif
    </div>
    <div x-show="isClosed" x-cloak class="mb-8">
        <div class="bg-green-50 border border-green-200 rounded-2xl py-4 text-center">
            <span class="text-sm font-bold text-green-700">✓ Week Closed</span>
        </div>
    </div>

</div>

@push('scripts')
<script>
function weeklyReview() {
    return {
        reviewId: {{ $review->id }},
        identityScore: {{ $review->identity_score ?? 'null' }},
        energyRating: {{ $review->energy_rating ?? 'null' }},
        reflection_win: '{{ addslashes($review->reflection_win ?? '') }}',
        reflection_challenge: '{{ addslashes($review->reflection_challenge ?? '') }}',
        reflection_learning: '{{ addslashes($review->reflection_learning ?? '') }}',
        reflection_gratitude: '{{ addslashes($review->reflection_gratitude ?? '') }}',
        identity_note: '{{ addslashes($review->identity_note ?? '') }}',
        next_week_focus: '{{ addslashes($review->next_week_focus ?? '') }}',
        priorities: @js($review->next_week_priorities ?? ['', '', '']),
        isClosed: {{ $review->is_completed ? 'true' : 'false' }},
        showConfirm: false,
        closing: false,

        async saveField(field, value) {
            const body = {};
            body[field] = value;
            await fetch('{{ url("admin/api/weekly-review") }}/' + this.reviewId, {
                method: 'PATCH',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify(body)
            });
        },

        async setIdentityScore(score) {
            this.identityScore = score;
            await this.saveField('identity_score', score);
        },

        async setEnergyRating(rating) {
            this.energyRating = rating;
            await this.saveField('energy_rating', rating);
        },

        async savePriorities() {
            await this.saveField('next_week_priorities', this.priorities);
        },

        fillNextPriority(text) {
            for (let i = 0; i < 3; i++) {
                if (!this.priorities[i]) {
                    this.priorities[i] = text;
                    this.savePriorities();
                    return;
                }
            }
        },

        async archiveTask(taskId) {
            await fetch('{{ url("admin/api/tasks") }}/' + taskId + '/archive', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            });
            // Remove the element from DOM
            event.target.closest('[class*="flex items-center justify-between"]').remove();
        },

        async closeWeek() {
            this.closing = true;
            const res = await fetch('{{ url("admin/api/weekly-review") }}/' + this.reviewId + '/complete', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            });
            if (res.ok) {
                this.isClosed = true;
                this.showConfirm = false;
                // Confetti
                if (typeof confetti === 'function') {
                    confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
                }
            }
            this.closing = false;
        }
    };
}
</script>
@endpush
@endsection
