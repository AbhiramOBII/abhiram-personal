@extends('admin.layouts.app')

@section('title', 'Today — ' . ($workingDay?->day_name ?? 'DayOS'))

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/localforage@1.10.0/dist/localforage.min.js"></script>
<style>
    .today-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, var(--day-color) 0%, transparent 60%);
        opacity: 0.08;
    }
    .focus-input {
        border-bottom: 2px solid var(--day-color);
    }
    .focus-input::placeholder { color: #cbd5e1; font-weight: 400; }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    [x-cloak] { display: none !important; }
    @keyframes wipPulse {
        0%, 100% { box-shadow: inset 0 0 0 0 rgba(0, 100, 148, 0); }
        50% { box-shadow: inset 3px 0 0 0 rgba(0, 100, 148, 0.5); }
    }
    .wip-pulse { animation: wipPulse 2s ease-in-out infinite; }
    @keyframes nudgeSlideUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes nudgeSlideDown {
        from { opacity: 1; transform: translateY(0); }
        to   { opacity: 0; transform: translateY(20px); }
    }
    .nudge-enter { animation: nudgeSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .nudge-exit  { animation: nudgeSlideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes nudgeProgress {
        from { width: 100%; }
        to   { width: 0%; }
    }
    .nudge-progress {
        height: 2px;
        animation: nudgeProgress linear forwards;
    }
</style>
@endpush

@php
    $hex = $workingDay?->hex_color ?? '#64748b';
    $nowTime = now()->format('H:i:s');
    $pillarColors = [
        'revenue'=>'yellow','operations'=>'gray','marketing'=>'orange','growth'=>'lime',
        'content'=>'sky','creation'=>'purple','product'=>'indigo','networking'=>'blue',
        'community'=>'teal','media'=>'pink','brand'=>'rose','podcast'=>'green',
        'health'=>'red','recovery'=>'emerald','learning'=>'violet',
    ];
    $pillarTw = [
        'yellow'=>['bg-yellow-100','text-yellow-700','border-yellow-200'],
        'gray'=>['bg-gray-100','text-gray-600','border-gray-200'],
        'orange'=>['bg-orange-100','text-orange-700','border-orange-200'],
        'lime'=>['bg-lime-100','text-lime-700','border-lime-200'],
        'sky'=>['bg-sky-100','text-sky-700','border-sky-200'],
        'purple'=>['bg-purple-100','text-purple-700','border-purple-200'],
        'indigo'=>['bg-indigo-100','text-indigo-700','border-indigo-200'],
        'blue'=>['bg-blue-100','text-blue-700','border-blue-200'],
        'teal'=>['bg-teal-100','text-teal-700','border-teal-200'],
        'pink'=>['bg-pink-100','text-pink-700','border-pink-200'],
        'rose'=>['bg-rose-100','text-rose-700','border-rose-200'],
        'green'=>['bg-green-100','text-green-700','border-green-200'],
        'red'=>['bg-red-100','text-red-600','border-red-200'],
        'emerald'=>['bg-emerald-100','text-emerald-700','border-emerald-200'],
        'violet'=>['bg-violet-100','text-violet-700','border-violet-200'],
    ];
    $pillarList = ['revenue','operations','marketing','growth','content','creation','product','networking','community','media','brand','podcast','health','recovery','learning'];
@endphp

@section('content')
<div class="max-w-[780px] mx-auto pb-32" style="--day-color: {{ $hex }}; --day-shadow: {{ $hex }}40;">

    {{-- ═══════════════════════════════════════════════
         PWA Install Banner
    ═══════════════════════════════════════════════ --}}
    <div x-data="pwaInstall()" x-cloak>
        <div x-show="showBanner" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-2 opacity-0"
             class="mb-5 rounded-xl border border-teal-200 bg-teal-50/60 px-4 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-[13px] text-slate-700 font-medium leading-tight truncate" x-text="iosDevice ? 'Tap Share → Add to Home Screen' : 'Install DayOS on your home screen'"></p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button x-show="!iosDevice" @click="install()" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-teal-600 border-0 cursor-pointer hover:bg-teal-700 transition-colors">Install</button>
                <button @click="dismiss()" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 border-0 bg-transparent cursor-pointer transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 1 — Day Theme Header
    ═══════════════════════════════════════════════ --}}
    <div class="today-header rounded-2xl sm:p-7 p-5 mb-6 relative overflow-hidden" style="border: 1px solid {{ $hex }}18; background: linear-gradient(135deg, {{ $hex }}06, white 70%);">
        <div class="relative flex items-start justify-between flex-wrap gap-3.5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: {{ $hex }}10; border: 1px solid {{ $hex }}15;">
                    <span class="text-[28px] leading-none">{{ $workingDay?->icon_emoji ?? '📅' }}</span>
                </div>
                <div>
                    <h1 class="font-['Space_Grotesk'] text-[22px] sm:text-2xl font-bold text-slate-900 tracking-tight">{{ $workingDay?->day_name ?? 'Today' }}</h1>
                    <p class="text-[13px] text-slate-500 mt-0.5">{{ $workingDay?->theme ?? '' }}</p>
                    @if($workingDay)
                        <span class="inline-flex items-center gap-1.5 mt-2.5 px-3 py-1 rounded-full text-[11px] font-semibold" style="background: {{ $hex }}10; color: {{ $hex }}; border: 1px solid {{ $hex }}20;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ $workingDay->energyLabel() }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-right pt-1">
                <p class="text-sm font-semibold text-slate-800">{{ now()->format('l') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5 tracking-wide">{{ now()->format('j F Y') }}</p>
                {{-- Sync indicator --}}
                <div x-data="syncIndicator()" class="flex items-center justify-end gap-1.5 mt-1.5">
                    <span x-show="syncPulse" x-transition class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                    <span x-show="lastSyncedAt" x-text="'Synced ' + lastSyncedAt" class="text-[10px] text-slate-400"></span>
                    <button @click="manualRefresh()" title="Refresh data" class="text-slate-400 hover:text-slate-600 p-0.5 leading-none border-0 bg-transparent cursor-pointer">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 2 — Block Timeline Bar
    ═══════════════════════════════════════════════ --}}
    @if($timeBlocks->count())
    <div class="mb-6 overflow-x-auto scrollbar-none py-0.5">
        <div class="flex gap-2 min-w-max">
            @foreach($timeBlocks as $block)
                @php
                    $isCurrent = $currentBlock && $currentBlock->id === $block->id;
                    $isPast = \Carbon\Carbon::parse($block->end_time)->format('H:i:s') < $nowTime;
                @endphp
                <div class="px-4 py-2.5 sm:px-4 sm:py-2.5 rounded-xl whitespace-nowrap text-xs font-medium transition-all shrink-0
                    {{ $isCurrent ? 'text-white font-semibold' : ($isPast ? 'bg-slate-50 text-slate-400 line-through decoration-slate-300' : 'bg-white text-slate-500 border border-slate-200') }}"
                    @if($isCurrent) style="background: {{ $hex }}; box-shadow: 0 3px 12px {{ $hex }}35;" @endif>
                    <span class="{{ $isCurrent ? 'font-semibold' : 'font-medium' }}">{{ $block->name }}</span>
                    <span class="ml-1 text-[11px] {{ $isCurrent ? 'opacity-85' : 'opacity-55' }}">{{ \Carbon\Carbon::parse($block->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($block->end_time)->format('H:i') }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 3 — Daily Quote
    ═══════════════════════════════════════════════ --}}
    <div class="mb-7 text-center py-4">
        <p class="text-lg sm:text-xl font-medium tracking-tight text-primary" style="color: #151828">
            "{{ $dailyQuote }}"
        </p>
        <div class="w-16 h-[2px] mx-auto mt-3 rounded-full" style="background: linear-gradient(90deg, {{ $hex }}, {{ $hex }}55);"></div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 4 — Progress Summary
    ═══════════════════════════════════════════════ --}}
    <div x-data="{ completed: {{ $completed }}, total: {{ $total }}, rolledOver: {{ $rolledOver }}, pct: {{ $completionPct }} }" class="mb-7">
        <div class="grid grid-cols-3 gap-2.5 mb-3">
            <div class="py-4 sm:py-[18px] px-3 sm:px-4 text-center rounded-xl bg-white border border-slate-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                <p class="text-2xl font-bold tracking-tight" style="color: {{ $hex }};" x-text="completed + '/' + total">{{ $completed }}/{{ $total }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-widest font-semibold flex items-center justify-center gap-1">
                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Done
                </p>
            </div>
            <div class="py-4 sm:py-[18px] px-3 sm:px-4 text-center rounded-xl bg-white border border-slate-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                <p class="text-2xl font-bold text-slate-800 tracking-tight" x-text="total - completed">{{ $total - $completed }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-widest font-semibold flex items-center justify-center gap-1">
                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pending
                </p>
            </div>
            <div class="py-4 sm:py-[18px] px-3 sm:px-4 text-center rounded-xl bg-white border border-slate-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                <p class="text-2xl font-bold text-amber-500 tracking-tight" x-text="rolledOver">{{ $rolledOver }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-widest font-semibold flex items-center justify-center gap-1">
                    <svg class="w-3 h-3 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Carried
                </p>
            </div>
        </div>
        <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
            <div x-ref="bar" class="h-full rounded-full transition-all duration-[1200ms] ease-out" style="background: linear-gradient(90deg, {{ $hex }}, {{ $hex }}aa); width: 0%;"
                 x-init="setTimeout(() => $refs.bar.style.width = pct + '%', 200)"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         Plan My Day Buttons
    ═══════════════════════════════════════════════ --}}
    <div class="flex items-center gap-2 mb-6">
        <button @click="planDay()" :disabled="planning"
                class="px-4 py-2.5 rounded-xl text-xs font-semibold text-white border-0 cursor-pointer transition-all hover:shadow-md flex items-center gap-2"
                :class="planning ? 'opacity-60 cursor-not-allowed' : ''"
                style="background: linear-gradient(135deg, {{ $hex }}, {{ $hex }}cc);">
            <span x-show="!planning">🧠 Plan My Day</span>
            <span x-show="planning">Planning...</span>
        </button>
        <button @click="autoSort()"
                class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 border border-slate-200 bg-white cursor-pointer transition-all hover:bg-slate-50 hover:shadow-sm flex items-center gap-2">
            ↕ Sort by Value
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 4a — Sunday Vision Mode Banner
    ═══════════════════════════════════════════════ --}}
    @if($isSundayVisionMode)
    <div class="mb-7 rounded-2xl p-5 border border-teal-200 bg-gradient-to-br from-teal-50/80 to-white">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-teal-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-teal-800">Today is Vision Day</p>
                    <p class="text-xs text-teal-600 mt-0.5">Your weekly review is waiting — reflect, reset, plan.</p>
                </div>
            </div>
            <a href="{{ route('admin.weekly-review.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-white no-underline transition-all hover:shadow-md shrink-0" style="background: linear-gradient(135deg, #4f98a3, #3d7f89);">
                Open Review
            </a>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 4ai — AI Daily Briefing
    ═══════════════════════════════════════════════ --}}
    @if(!empty($aiBriefing))
    <div class="mb-7 rounded-2xl bg-white border border-slate-200 p-5 relative overflow-hidden" style="border-left: 3px solid {{ $hex }};">
        <div class="flex items-start gap-3.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background: {{ $hex }}12;">
                <svg class="w-4 h-4" style="color: {{ $hex }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold mb-2">Daily Briefing</p>
                <p class="text-[13px] text-slate-700 leading-relaxed">{{ $aiBriefing }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 4b — Behavioral Practices (checkbox only)
    ═══════════════════════════════════════════════ --}}
    @if($behavioralPractices->count() || $practiceLogs->where('practice.type', '!=', 'reflective')->count())
    @php
        $bhLogs = $practiceLogs->filter(fn($l) => !$l->practice->isReflective());
        $bhDone = $bhLogs->where('is_completed', true)->count();
        $bhTotal = $bhLogs->count();
    @endphp
    <div class="mb-7" x-data="{ bhDone: {{ $bhDone }} }" @bh-toggle.window="bhDone += $event.detail">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <span class="text-[15px]">✅</span>
                <span class="text-[13px] font-bold text-slate-800 tracking-tight">Behavioral Practices</span>
            </div>
            <span class="px-3 py-0.5 rounded-xl text-[11px] font-semibold" style="background: {{ $hex }}10; color: {{ $hex }}; border: 1px solid {{ $hex }}20;" x-text="bhDone + ' / {{ $bhTotal }}'">{{ $bhDone }} / {{ $bhTotal }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            @foreach($bhLogs as $log)
                @php $p = $log->practice; $streak = $p->currentStreak(); @endphp
                <div x-data="{ completed: {{ $log->is_completed ? 'true' : 'false' }}, loading: false }"
                     class="flex items-center gap-3.5 px-4 py-3 border-b border-slate-100 last:border-b-0 transition-all"
                     :class="completed ? 'bg-slate-50/50' : 'bg-white'">

                    {{-- Checkbox --}}
                    <button @click="
                        loading=true;
                        if(completed){
                            fetch('{{ url('admin/api/practices') }}/{{ $p->id }}/uncomplete',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>{completed=false;loading=false;$dispatch('bh-toggle',-1);});
                        } else {
                            fetch('{{ url('admin/api/practices') }}/{{ $p->id }}/complete',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>{completed=true;loading=false;$dispatch('bh-toggle',1);});
                        }"
                        :disabled="loading"
                        class="w-[26px] h-[26px] rounded-lg cursor-pointer flex items-center justify-center transition-all duration-200 shrink-0"
                        style="border: 2px solid {{ $p->hex_color }};"
                        :style="completed ? 'background: {{ $p->hex_color }}; border-color: {{ $p->hex_color }};' : 'background: transparent;'">
                        <svg x-show="completed" x-transition.scale class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </button>

                    {{-- Icon + Name --}}
                    <div class="flex items-center gap-2.5 flex-1 min-w-0">
                        <x-practice-icon :practice="$p" size="20" />
                        <span class="text-[13px] font-medium transition-all duration-200"
                              :class="completed ? 'line-through text-slate-400' : 'text-slate-700'">{{ $p->name }}</span>
                        @if($streak >= 3)
                            <span class="text-[11px] inline-flex items-center gap-0.5 bg-orange-50 px-1.5 py-px rounded-md font-semibold text-orange-600 shrink-0">🔥 {{ $streak }}</span>
                        @endif
                    </div>

                    {{-- Quantity (if quantified) --}}
                    @if($p->isQuantified())
                    <div x-data="{ qty: {{ $log->quantity ?? 0 }} }" class="flex items-center gap-1 shrink-0">
                        <input type="number" x-model.number="qty" min="0"
                            @change="fetch('{{ url('admin/api/practice-logs/quantity') }}', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: JSON.stringify({practice_id: {{ $p->id }}, quantity: qty, date: '{{ now()->toDateString() }}'}) }).then(r=>r.json()).then(d=>{ if(d.auto_completed && !completed){ completed=true; $dispatch('bh-toggle',1); } })"
                            class="w-12 px-1.5 py-1 rounded-lg border border-slate-200 text-center text-xs text-slate-600 outline-none bg-slate-50 focus:border-slate-300">
                        <span class="text-[10px] text-slate-400 whitespace-nowrap">/ {{ $p->target_value }} {{ $p->unit }}</span>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 4b2 — Reflective Practices (textbox)
    ═══════════════════════════════════════════════ --}}
    @if($reflectivePractices->count())
    @php $refDone = $reflectivePractices->filter(fn($p) => $p->logs->first()?->response_text)->count(); @endphp
    <div class="mb-7">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <span class="text-[15px]">🧘</span>
                <span class="text-[13px] font-bold text-slate-800 tracking-tight">Reflective Practices</span>
            </div>
            <span class="px-3 py-0.5 rounded-xl text-[11px] font-semibold" style="background: {{ $hex }}10; color: {{ $hex }}; border: 1px solid {{ $hex }}20;">{{ $refDone }} / {{ $reflectivePractices->count() }}</span>
        </div>
        <div class="space-y-3">
            @foreach($reflectivePractices as $rp)
                @php
                    $rLog = $rp->logs->first();
                    $aiPrompt = $rLog?->ai_prompt_used ?? '';
                    $responseText = $rLog?->response_text ?? '';
                @endphp
                <div x-data="{ response: `{{ str_replace(['`','\\'], ['\\`','\\\\'], $responseText) }}`, saving: false, saved: false, prompt: `{{ str_replace(['`','\\'], ['\\`','\\\\'], $aiPrompt) }}` }"
                     class="bg-white border border-slate-200 rounded-2xl p-4 transition-shadow hover:shadow-md">

                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $rp->hex_color }}12;">
                            <x-practice-icon :practice="$rp" size="20" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-semibold text-slate-800">{{ $rp->name }}</span>
                            @if($rp->description)<p class="text-[11px] text-slate-400 mt-0.5">{{ $rp->description }}</p>@endif
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5">
                            <span x-show="saving" class="text-[10px] text-slate-400 font-medium">Saving…</span>
                            <span x-show="saved" x-transition.opacity class="text-[10px] text-emerald-600 font-semibold flex items-center gap-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Saved
                            </span>
                            <div class="w-2 h-2 rounded-full shrink-0" :class="response?.trim() ? 'bg-emerald-400' : 'bg-slate-200'"></div>
                        </div>
                    </div>

                    {{-- AI Prompt --}}
                    <template x-if="prompt">
                        <div class="mb-3 px-3.5 py-2.5 rounded-xl bg-indigo-50/60 border border-indigo-100">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Today's Prompt</p>
                            <p class="text-[13px] text-indigo-800 leading-relaxed italic" x-text="prompt"></p>
                        </div>
                    </template>

                    {{-- Response textarea --}}
                    <textarea x-model="response"
                        @blur="if(response !== `{{ str_replace(['`','\\'], ['\\`','\\\\'], $responseText) }}`){ saving=true; fetch('{{ url('admin/api/practice-logs/response') }}', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: JSON.stringify({practice_id: {{ $rp->id }}, response_text: response, date: '{{ now()->toDateString() }}'}) }).then(()=>{ saving=false; saved=true; setTimeout(()=>saved=false, 2000); }); }"
                        placeholder="{{ $rp->input_type === 'list' ? '- Item 1\n- Item 2\n- Item 3' : 'Write your reflection…' }}"
                        rows="{{ $rp->input_type === 'text_short' ? 2 : ($rp->input_type === 'list' ? 4 : 3) }}"
                        class="w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 bg-slate-50/80 resize-y outline-none transition-all duration-200 focus:border-indigo-300 focus:bg-white focus:shadow-sm placeholder:text-slate-300"></textarea>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 4c — Upskilling Widget
    ═══════════════════════════════════════════════ --}}
    <div x-data="upskillWidget()" class="mb-7">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span class="text-[13px] font-bold text-slate-800 tracking-tight">Today's Learning</span>
            </div>
            @if($workingDay?->upskill_focus)
                <span class="text-[11px] text-slate-400">Focus: {{ $workingDay->upskill_focus }}</span>
            @endif
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow p-[18px]">
            @if($upskillSuggestion)
                @php $domain = $upskillSuggestion->skillDomain; @endphp
                {{-- Suggested item --}}
                <div class="flex items-start gap-3 mb-3.5">
                    <span class="text-2xl leading-none">{{ $domain->icon_emoji ?? '📚' }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wide mb-0.5" style="color: {{ $domain->hex_color ?? '#a86fdf' }};">{{ $domain->name }}</p>
                        <p class="text-sm font-medium text-slate-800">{{ $upskillSuggestion->title }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-500">{{ ucfirst($upskillSuggestion->type) }}</span>
                            @if($upskillSuggestion->estimated_hours)
                                <span class="text-[11px] text-slate-400">~{{ $upskillSuggestion->estimated_hours }}h</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- No active session state --}}
                <div x-show="!sessionActive">
                    <button @click="startSession()" :disabled="starting"
                        class="w-full py-3 rounded-xl border-0 cursor-pointer text-sm font-semibold text-white bg-purple-500 transition-all shadow-[0_2px_8px_rgba(168,111,223,0.3)] hover:bg-purple-600 disabled:opacity-60 disabled:cursor-default flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        Start Session
                    </button>
                </div>

                {{-- Active session state --}}
                <div x-show="sessionActive" x-cloak>
                    <div class="flex items-center gap-2.5 mb-3 px-3 py-2.5 rounded-xl bg-purple-50 border border-purple-200">
                        <span class="w-2 h-2 rounded-full bg-purple-500" style="animation: pulse 1.5s infinite;"></span>
                        <span class="text-sm font-semibold text-purple-700 tabular-nums" x-text="timerDisplay"></span>
                        <span class="text-[11px] text-purple-400">in progress</span>
                    </div>
                    <textarea x-model="sessionNotes"
                        @blur="saveNotes()"
                        placeholder="Capture notes during this session..."
                        rows="2"
                        class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-xs text-slate-600 bg-slate-50 resize-y outline-none font-[inherit] mb-2.5 focus:border-slate-400 transition-colors"></textarea>
                    <button @click="showEndForm = true" x-show="!showEndForm"
                        class="w-full py-2.5 rounded-lg border border-slate-200 bg-white cursor-pointer text-[13px] font-semibold text-red-500 transition-all hover:bg-red-50 hover:border-red-200 flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="1"/></svg>
                        End Session
                    </button>
                    <div x-show="showEndForm" x-transition class="mt-2">
                        <div class="mb-2">
                            <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Duration (minutes)</label>
                            <input type="number" x-model="endDuration" :placeholder="Math.round(elapsedSeconds / 60)" min="1" class="w-[100px] px-2.5 py-2 rounded-lg border border-slate-200 text-[13px] text-slate-800 outline-none focus:border-slate-400">
                        </div>
                        <div class="mb-2.5">
                            <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Key Takeaway</label>
                            <input type="text" x-model="endTakeaway" placeholder="What did you learn?" class="w-full px-2.5 py-2 rounded-lg border border-slate-200 text-[13px] text-slate-800 outline-none focus:border-slate-400">
                        </div>
                        <button @click="endSession()" :disabled="ending"
                            class="w-full py-3 rounded-xl border-0 cursor-pointer text-sm font-semibold text-white bg-red-500 transition-all hover:bg-red-600 disabled:opacity-60">
                            Confirm End
                        </button>
                    </div>
                </div>
            @else
                <p class="text-[13px] text-slate-400 text-center py-3">No pending learning items. <a href="{{ route('admin.upskilling.index') }}" class="text-purple-500 no-underline font-medium">Add one →</a></p>
            @endif

            {{-- Footer --}}
            <div class="flex items-center justify-between mt-3.5 pt-3 border-t border-slate-100">
                <span class="text-xs text-slate-500 font-medium">{{ $todayLearningMinutes }} min today</span>
                <a href="{{ route('admin.upskilling.index') }}" class="text-xs text-purple-500 no-underline font-medium">View all learning →</a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 4b — Deadline Alerts Banner
    ═══════════════════════════════════════════════ --}}
    @if($deadlineAlerts->isNotEmpty())
    <div class="mb-5 flex flex-col gap-2">
        @foreach($deadlineAlerts as $alert)
        @if($alert->task && $alert->task->deadline_badge)
        <div x-data="{ visible: true }" x-show="visible"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="flex items-start gap-3 p-3.5 rounded-xl"
             style="background: {{ $alert->task->deadline_badge['bg'] }}; border: 1px solid {{ $alert->task->deadline_badge['color'] }}44;">
            <span class="text-[18px] shrink-0 mt-0.5">{{ $alert->task->deadline_badge['icon'] }}</span>
            <div class="flex-1 min-w-0">
                <span class="text-[10px] font-bold uppercase tracking-widest" style="color: {{ $alert->task->deadline_badge['color'] }};">
                    {{ $alert->task->deadline_badge['label'] }} — {{ $alert->task->title }}
                </span>
                <p class="text-[13px] text-slate-700 mt-0.5 leading-snug">{{ $alert->ai_message }}</p>
            </div>
            <button @click="visible = false; fetch('{{ url('admin/api/deadline-alerts') }}/{{ $alert->id }}/dismiss', { method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })"
                    class="text-slate-400 shrink-0 mt-0.5 bg-transparent border-0 cursor-pointer hover:opacity-70 text-sm">✕</button>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 4c — Project Tasks
    ═══════════════════════════════════════════════ --}}
    @if($projectTasks->isNotEmpty() || $overdueProjects->isNotEmpty())
    <div x-data="{ projOpen: true }" class="mb-6">
        <button @click="projOpen = !projOpen"
                class="flex items-center gap-2 w-full mb-2.5 px-1 bg-transparent border-0 cursor-pointer">
            <span class="text-[15px]">🗂️</span>
            <span class="text-[13px] font-bold text-slate-800 tracking-tight">Project Tasks</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500">{{ $projectTasks->count() + $overdueProjects->count() }}</span>
            <svg x-show="!projOpen" class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="6,9 12,15 18,9"/></svg>
            <svg x-show="projOpen" class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="18,15 12,9 6,15"/></svg>
        </button>
        <div x-show="projOpen" x-transition class="flex flex-col gap-2">
            @foreach($overdueProjects as $task)
                @include('dashboard.partials.project-task-card', ['task' => $task])
            @endforeach
            @foreach($projectTasks as $task)
                @include('dashboard.partials.project-task-card', ['task' => $task])
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 5 — Task List
    ═══════════════════════════════════════════════ --}}
    <div x-data="taskList()" x-init="init()">

        {{-- Block-grouped tasks --}}
        @foreach($timeBlocks as $block)
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-2.5 px-1">
                    <span class="text-[13px] font-bold text-slate-800 tracking-tight">{{ $block->name }}</span>
                    <span class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($block->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($block->end_time)->format('H:i') }}</span>
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 px-1.5"
                          x-text="(groups['{{ $block->id }}'] || []).filter(t=> t.status !== 'done').length"></span>
                </div>
                <div class="task-group bg-white border border-slate-200 rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow min-h-[4px] overflow-visible" data-block-id="{{ $block->id }}">
                    <template x-for="task in sortedGroup('{{ $block->id }}')" :key="task.id">
                        <div class="border-b border-slate-100 last:border-b-0" :data-task-id="task.id" data-task-card>
                            {{-- Desktop: single row --}}
                            <div class="hidden md:flex items-center gap-3 px-4 py-3.5 bg-white relative transition-all cursor-pointer active:bg-slate-50"
                                 :style="(task.is_rolled_over ? 'border-left:3px solid #f59e0b;' : '') + (task.status === 'done' ? 'opacity:0.4;' : '')"
                                 :class="task.status === 'wip' && 'wip-pulse'"
                                 @click="openTaskAction(task)">
                                <div class="drag-handle cursor-grab text-slate-300 shrink-0 touch-none p-1" @click.stop>
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
                                </div>
                                <span class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 text-[13px] leading-none"
                                      :style="'background:' + (STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).bg + ';color:' + (STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).color"
                                      x-text="(STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).emoji"></span>
                                <span class="w-2 h-2 rounded-full shrink-0" :style="'background:' + priorityColor(task.priority)"></span>
                                <span class="flex-1 min-w-0 text-sm font-medium text-slate-800 truncate" :class="task.status === 'done' && 'line-through !text-slate-400'" x-text="task.title"></span>
                                <template x-if="task.is_rolled_over">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-800 whitespace-nowrap">↩ <span x-text="task.rollover_count"></span></span>
                                </template>
                                <span x-show="task.value_score > 0"
                                      class="rounded-full px-1.5 py-0.5 font-mono font-semibold shrink-0"
                                      :style="'font-size:10px;background:' + scoreBadgeBg(task.value_score) + ';color:' + scoreBadgeColor(task.value_score) + ';'"
                                      x-text="'VS·' + task.value_score"></span>
                                <span class="shrink-0" @click.stop>
                                    <span x-data="tbcbPill(task)" class="relative inline-flex">
                                        <button @click="open = !open"
                                                class="px-1.5 py-0.5 rounded-md text-[10px] font-bold whitespace-nowrap inline-flex items-center gap-1 border cursor-pointer transition-all"
                                                :class="task.due_date ? (isOverdue(task.due_date) ? 'bg-red-50 text-red-600 border-red-200' : 'bg-indigo-50 text-indigo-600 border-indigo-200') : 'bg-slate-50 text-slate-400 border-slate-200'">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span x-text="task.due_date ? 'TBCB ' + fmtDate(task.due_date) : 'TBCB'"></span>
                                        </button>
                                        <div x-show="open" x-cloak @click.outside="open = false"
                                             class="absolute right-0 top-6 z-50 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5" style="min-width: 160px;">
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 px-0.5">Complete by</p>
                                            <input type="date" :value="task.due_date ? task.due_date.substring(0,10) : ''"
                                                   class="w-full text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-800 outline-none"
                                                   @change="setTbcb(task, $event.target.value)">
                                            <button x-show="task.due_date" @click="setTbcb(task, null)"
                                                    class="w-full mt-1.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-600 text-[10px] font-semibold cursor-pointer">Clear</button>
                                        </div>
                                    </span>
                                </span>
                            </div>
                            {{-- Mobile: 3-row card --}}
                            <div class="md:hidden px-3 py-3 bg-white relative transition-all cursor-pointer active:bg-slate-50"
                                 :style="(task.is_rolled_over ? 'border-left:3px solid #f59e0b;' : '') + (task.status === 'done' ? 'opacity:0.4;' : '')"
                                 @click="openTaskAction(task)">
                                {{-- Row 1: Drag, Status pill, VS pill, Rollover pill, Priority --}}
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="drag-handle cursor-grab text-slate-300 shrink-0 touch-none" @click.stop>
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold shrink-0"
                                          :style="'background:' + (STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).bg + ';color:' + (STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).color"
                                          x-text="task.status === 'wip' ? 'WIP' : (task.status === 'done' ? 'Done' : (task.status === 'deferred' ? 'Deferred' : 'Backlog'))"></span>
                                    <span x-show="task.value_score > 0"
                                          class="px-2.5 py-1 rounded-full font-mono font-bold shrink-0 text-[11px]"
                                          :style="'background:' + scoreBadgeBg(task.value_score) + ';color:' + scoreBadgeColor(task.value_score) + ';'"
                                          x-text="'VS·' + task.value_score"></span>
                                    <template x-if="task.is_rolled_over">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 whitespace-nowrap">↩<span x-text="task.rollover_count"></span></span>
                                    </template>
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0 ml-auto" :style="'background:' + priorityColor(task.priority)"></span>
                                </div>
                                {{-- Row 2: Full title --}}
                                <p class="text-[15px] font-semibold text-slate-800 leading-snug pl-6 mb-2"
                                   :class="task.status === 'done' && 'line-through !text-slate-400'" x-text="task.title"></p>
                                {{-- Row 3: TBCB pill + Pillar --}}
                                <div class="pl-6 flex items-center gap-2" @click.stop>
                                    <span x-data="tbcbPill(task)" class="relative inline-flex">
                                        <button @click="open = !open"
                                                class="px-3 py-1 rounded-full text-[11px] font-bold whitespace-nowrap inline-flex items-center gap-1.5 border cursor-pointer transition-all"
                                                :class="task.due_date ? (isOverdue(task.due_date) ? 'bg-red-50 text-red-600 border-red-200' : 'bg-indigo-50 text-indigo-600 border-indigo-200') : 'bg-slate-100 text-slate-400 border-slate-200'">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span x-text="task.due_date ? 'TBCB ' + fmtDate(task.due_date) : 'TBCB'"></span>
                                        </button>
                                        <div x-show="open" x-cloak @click.outside="open = false"
                                             class="absolute left-0 bottom-8 z-50 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5" style="min-width: 160px;">
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 px-0.5">Complete by</p>
                                            <input type="date" :value="task.due_date ? task.due_date.substring(0,10) : ''"
                                                   class="w-full text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-800 outline-none"
                                                   @change="setTbcb(task, $event.target.value)">
                                            <button x-show="task.due_date" @click="setTbcb(task, null)"
                                                    class="w-full mt-1.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-600 text-[10px] font-semibold cursor-pointer">Clear</button>
                                        </div>
                                    </span>
                                    <template x-if="task.pillar">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600" x-text="task.pillar"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        @endforeach

        {{-- Anytime tasks --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2.5 px-1">
                <span class="text-[13px] font-bold text-slate-800 tracking-tight">Anytime</span>
                <span class="inline-flex items-center justify-center min-w-[20px] h-5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 px-1.5"
                      x-text="(groups['anytime'] || []).filter(t=> t.status !== 'done').length"></span>
            </div>
            <div class="task-group bg-white border border-slate-200 rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow min-h-[4px] overflow-visible" data-block-id="anytime">
                <template x-for="task in sortedGroup('anytime')" :key="task.id">
                    <div class="border-b border-slate-100 last:border-b-0" :data-task-id="task.id" data-task-card>
                        {{-- Desktop: single row --}}
                        <div class="hidden md:flex items-center gap-3 px-4 py-3.5 bg-white relative transition-all cursor-pointer active:bg-slate-50"
                             :style="(task.is_rolled_over ? 'border-left:3px solid #f59e0b;' : '') + (task.status === 'done' ? 'opacity:0.4;' : '')"
                             :class="task.status === 'wip' && 'wip-pulse'"
                             @click="openTaskAction(task)">
                            <div class="drag-handle cursor-grab text-slate-300 shrink-0 touch-none p-1" @click.stop>
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
                            </div>
                            <span class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 text-[13px] leading-none"
                                  :style="'background:' + (STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).bg + ';color:' + (STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).color"
                                  x-text="(STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).emoji"></span>
                            <span class="w-2 h-2 rounded-full shrink-0" :style="'background:' + priorityColor(task.priority)"></span>
                            <span class="flex-1 min-w-0 text-sm font-medium text-slate-800 truncate" :class="task.status === 'done' && 'line-through !text-slate-400'" x-text="task.title"></span>
                            <template x-if="task.is_rolled_over">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-800 whitespace-nowrap">↩ <span x-text="task.rollover_count"></span></span>
                            </template>
                            <span x-show="task.value_score > 0"
                                  class="rounded-full px-1.5 py-0.5 font-mono font-semibold shrink-0"
                                  :style="'font-size:10px;background:' + scoreBadgeBg(task.value_score) + ';color:' + scoreBadgeColor(task.value_score) + ';'"
                                  x-text="'VS·' + task.value_score"></span>
                            <span class="shrink-0" @click.stop>
                                <span x-data="tbcbPill(task)" class="relative inline-flex">
                                    <button @click="open = !open"
                                            class="px-1.5 py-0.5 rounded-md text-[10px] font-bold whitespace-nowrap inline-flex items-center gap-1 border cursor-pointer transition-all"
                                            :class="task.due_date ? (isOverdue(task.due_date) ? 'bg-red-50 text-red-600 border-red-200' : 'bg-indigo-50 text-indigo-600 border-indigo-200') : 'bg-slate-50 text-slate-400 border-slate-200'">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span x-text="task.due_date ? 'TBCB ' + fmtDate(task.due_date) : 'TBCB'"></span>
                                    </button>
                                    <div x-show="open" x-cloak @click.outside="open = false"
                                         class="absolute right-0 top-6 z-50 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5" style="min-width: 160px;">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 px-0.5">Complete by</p>
                                        <input type="date" :value="task.due_date ? task.due_date.substring(0,10) : ''"
                                               class="w-full text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-800 outline-none"
                                               @change="setTbcb(task, $event.target.value)">
                                        <button x-show="task.due_date" @click="setTbcb(task, null)"
                                                class="w-full mt-1.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-600 text-[10px] font-semibold cursor-pointer">Clear</button>
                                    </div>
                                </span>
                            </span>
                        </div>
                        {{-- Mobile: 3-row card --}}
                        <div class="md:hidden px-3 py-3 bg-white relative transition-all cursor-pointer active:bg-slate-50"
                             :style="(task.is_rolled_over ? 'border-left:3px solid #f59e0b;' : '') + (task.status === 'done' ? 'opacity:0.4;' : '')"
                             @click="openTaskAction(task)">
                            {{-- Row 1: Drag, Status pill, VS pill, Rollover pill, Priority --}}
                            <div class="flex items-center gap-2 mb-2">
                                <div class="drag-handle cursor-grab text-slate-300 shrink-0 touch-none" @click.stop>
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold shrink-0"
                                      :style="'background:' + (STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).bg + ';color:' + (STATUS_CONFIG[task.status]||STATUS_CONFIG.backlog).color"
                                      x-text="task.status === 'wip' ? 'WIP' : (task.status === 'done' ? 'Done' : (task.status === 'deferred' ? 'Deferred' : 'Backlog'))"></span>
                                <span x-show="task.value_score > 0"
                                      class="px-2.5 py-1 rounded-full font-mono font-bold shrink-0 text-[11px]"
                                      :style="'background:' + scoreBadgeBg(task.value_score) + ';color:' + scoreBadgeColor(task.value_score) + ';'"
                                      x-text="'VS·' + task.value_score"></span>
                                <template x-if="task.is_rolled_over">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 whitespace-nowrap">↩<span x-text="task.rollover_count"></span></span>
                                </template>
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 ml-auto" :style="'background:' + priorityColor(task.priority)"></span>
                            </div>
                            {{-- Row 2: Full title --}}
                            <p class="text-[15px] font-semibold text-slate-800 leading-snug pl-6 mb-2"
                               :class="task.status === 'done' && 'line-through !text-slate-400'" x-text="task.title"></p>
                            {{-- Row 3: TBCB pill + Pillar --}}
                            <div class="pl-6 flex items-center gap-2" @click.stop>
                                <span x-data="tbcbPill(task)" class="relative inline-flex">
                                    <button @click="open = !open"
                                            class="px-3 py-1 rounded-full text-[11px] font-bold whitespace-nowrap inline-flex items-center gap-1.5 border cursor-pointer transition-all"
                                            :class="task.due_date ? (isOverdue(task.due_date) ? 'bg-red-50 text-red-600 border-red-200' : 'bg-indigo-50 text-indigo-600 border-indigo-200') : 'bg-slate-100 text-slate-400 border-slate-200'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span x-text="task.due_date ? 'TBCB ' + fmtDate(task.due_date) : 'TBCB'"></span>
                                    </button>
                                    <div x-show="open" x-cloak @click.outside="open = false"
                                         class="absolute left-0 top-8 z-50 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5" style="min-width: 160px;">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 px-0.5">Complete by</p>
                                        <input type="date" :value="task.due_date ? task.due_date.substring(0,10) : ''"
                                               class="w-full text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-800 outline-none"
                                               @change="setTbcb(task, $event.target.value)">
                                        <button x-show="task.due_date" @click="setTbcb(task, null)"
                                                class="w-full mt-1.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-600 text-[10px] font-semibold cursor-pointer">Clear</button>
                                    </div>
                                </span>
                                <template x-if="task.pillar">
                                    <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600" x-text="task.pillar"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             Task Action Popup (Bottom Sheet)
        ═══════════════════════════════════════════════ --}}
        <div x-show="actionTask" x-cloak>
            {{-- Overlay --}}
            <div x-show="actionTask" x-transition.opacity @click="actionTask = null"
                 class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm z-[60]"></div>
            {{-- Sheet --}}
            <div x-show="actionTask" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                 class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl px-5 pt-4 pb-8 z-[70] max-w-[480px] mx-auto shadow-[0_-8px_40px_rgba(0,0,0,0.12)]">

                {{-- Drag handle --}}
                <div class="flex justify-center mb-4">
                    <div class="w-9 h-1 rounded-sm bg-slate-200"></div>
                </div>

                {{-- Task title --}}
                <div class="flex items-center gap-3 mb-5 px-1">
                    <span class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 text-[14px] leading-none"
                          :style="actionTask && 'background:' + (STATUS_CONFIG[actionTask.status]||STATUS_CONFIG.backlog).bg + ';color:' + (STATUS_CONFIG[actionTask.status]||STATUS_CONFIG.backlog).color"
                          x-text="actionTask && (STATUS_CONFIG[actionTask.status]||STATUS_CONFIG.backlog).emoji"></span>
                    <span class="text-[15px] font-semibold text-slate-800 truncate flex-1" x-text="actionTask?.title"></span>
                    <button @click="actionTask = null" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 border-0 bg-transparent cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Status section --}}
                <div class="mb-5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2.5 px-1">Status</p>
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="s in ['backlog','wip','done','deferred']" :key="s">
                            <button @click="setTaskStatus(actionTask, s)"
                                class="flex flex-col items-center gap-1.5 py-3 rounded-xl border-2 cursor-pointer transition-all active:scale-95"
                                :class="actionTask?.status === s ? 'border-slate-800 bg-slate-50 shadow-sm' : 'border-slate-100 bg-white hover:border-slate-200'"
                                :style="actionTask?.status === s ? 'border-color:' + (STATUS_CONFIG[s]||STATUS_CONFIG.backlog).color : ''">
                                <span class="text-lg leading-none" x-text="(STATUS_CONFIG[s]||STATUS_CONFIG.backlog).emoji"></span>
                                <span class="text-[11px] font-semibold" :class="actionTask?.status === s ? 'text-slate-800' : 'text-slate-500'" x-text="(STATUS_CONFIG[s]||STATUS_CONFIG.backlog).label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Importance section --}}
                <div class="mb-5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2.5 px-1">Importance</p>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="p in ['must','should','bonus']" :key="p">
                            <button @click="setTaskPriority(actionTask, p)"
                                class="flex items-center justify-center gap-2 py-3 rounded-xl border-2 cursor-pointer transition-all active:scale-95"
                                :class="actionTask?.priority === p ? 'shadow-sm' : 'border-slate-100 bg-white hover:border-slate-200'"
                                :style="actionTask?.priority === p ? 'border-color:' + priorityColor(p) + ';background:' + priorityColor(p) + '08;' : ''">
                                <span class="w-2.5 h-2.5 rounded-full" :style="'background:' + priorityColor(p)"></span>
                                <span class="text-[12px] font-semibold" :class="actionTask?.priority === p ? 'text-slate-800' : 'text-slate-500'" x-text="p.charAt(0).toUpperCase() + p.slice(1)"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex gap-2">
                    <button @click="deferTask(actionTask); actionTask = null;"
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl border border-amber-200 bg-amber-50 cursor-pointer transition-all active:scale-95 hover:bg-amber-100">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <span class="text-[12px] font-semibold text-amber-700">Defer to Tomorrow</span>
                    </button>
                    <button @click="if(confirm('Delete this task?')){ deleteTask(actionTask); actionTask = null; }"
                        class="flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl border border-red-200 bg-red-50 cursor-pointer transition-all active:scale-95 hover:bg-red-100">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span class="text-[12px] font-semibold text-red-600">Delete</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             Plan My Day Modal
        ═══════════════════════════════════════════════ --}}
        <div x-show="planModalOpen" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
             style="background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);"
             @keydown.escape.window="planModalOpen = false" @click="planModalOpen = false">
            <div x-show="planModalOpen"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                 @click.stop
                 class="w-full flex flex-col"
                 style="max-width:720px; max-height:88vh; background: white; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); overflow:hidden;">
                {{-- Header --}}
                <div class="flex items-center justify-between p-5" style="border-bottom: 1px solid #f1f5f9;">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">🧠 Your Day Plan</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Sorted by Value Score. Confirm to apply.</p>
                    </div>
                    <button @click="planModalOpen = false" class="text-slate-400 hover:text-slate-600 bg-transparent border-0 cursor-pointer text-lg">✕</button>
                </div>
                {{-- AI Rationale --}}
                <div x-show="planRationale" class="px-5 py-3" style="background: #eff6ff; border-bottom: 1px solid #f1f5f9;">
                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">✨ AI Morning Briefing</span>
                    <p x-text="planRationale" class="text-sm text-slate-700 mt-1 leading-relaxed"></p>
                </div>
                {{-- Task list --}}
                <div class="flex-1 overflow-y-auto p-5 flex flex-col gap-2">
                    <template x-for="(task, index) in planTasks" :key="task.id">
                        <div class="flex items-center gap-3 p-3 rounded-xl" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <span class="font-mono font-bold w-6 text-center shrink-0 text-xs text-slate-400" x-text="index + 1"></span>
                            <span class="rounded-full px-2 py-0.5 font-mono font-semibold shrink-0"
                                  :style="'font-size:10px;background:' + scoreBadgeBg(task.value_score) + ';color:' + scoreBadgeColor(task.value_score)"
                                  x-text="'VS·' + task.value_score"></span>
                            <div class="flex-1 min-w-0">
                                <span x-text="task.title" class="text-sm font-medium text-slate-800"></span>
                                <div class="flex gap-2 mt-0.5 flex-wrap">
                                    <span x-text="task.pillar" class="text-[10px] text-slate-500"></span>
                                    <span x-show="task.deadline_formatted" x-text="task.deadline_formatted" class="text-[10px] text-amber-600"></span>
                                </div>
                            </div>
                            <div class="shrink-0 text-right" style="font-size:10px; color:#94a3b8; line-height:1.6;">
                                <div x-text="'Impact: ' + (task.impact_rating * 10)"></div>
                                <div x-text="'Urgency: ' + task.urgency_score"></div>
                                <div x-text="'Theme: ' + task.theme_score"></div>
                            </div>
                        </div>
                    </template>
                </div>
                {{-- Resurface candidates --}}
                <template x-if="resurfaceCandidates.length > 0">
                    <div style="border-top: 1px solid #f1f5f9;">
                        <div class="px-5 pt-4 pb-2">
                            <h4 class="text-sm font-semibold text-slate-600">💤 Light day — resurface these?</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Deferred or low-priority tasks that can fill your day.</p>
                        </div>
                        <div class="px-5 pb-4 flex flex-col gap-2">
                            <template x-for="task in resurfaceCandidates" :key="task.id">
                                <label class="flex items-center gap-3 p-2.5 rounded-lg cursor-pointer" style="background: #f8fafc; border: 1px dashed #e2e8f0;">
                                    <input type="checkbox" :value="task.id" x-model="selectedResurface"
                                           class="w-4 h-4 rounded" style="accent-color: {{ $hex }};">
                                    <span x-text="task.title" class="text-sm flex-1"></span>
                                    <span x-text="task.status === 'deferred' ? '⏭️ Deferred' : '📋 Low VS·' + task.value_score"
                                          class="text-[10px] text-slate-400"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
                {{-- Footer --}}
                <div class="flex items-center justify-between p-5" style="border-top: 1px solid #f1f5f9;">
                    <button @click="planModalOpen = false"
                            class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 border border-slate-200 bg-white cursor-pointer hover:bg-slate-50">Cancel</button>
                    <button @click="confirmPlan()"
                            class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white border-0 cursor-pointer hover:shadow-md"
                            style="background: {{ $hex }};">✅ Confirm Plan →</button>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 5b — Wrap Up Day / Rollover
    ═══════════════════════════════════════════════ --}}
    @if($total - $completed > 0)
    <div x-data="{ rolling: false, done: false, message: '' }" class="mb-7">
        <div class="rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50/60 to-white p-5">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800" x-show="!done">{{ $total - $completed }} incomplete task{{ $total - $completed !== 1 ? 's' : '' }}</p>
                        <p class="text-sm font-bold text-emerald-700" x-show="done" x-text="message" x-cloak></p>
                        <p class="text-xs text-slate-500 mt-0.5" x-show="!done">Roll them over to tomorrow so nothing gets lost.</p>
                    </div>
                </div>
                <button
                    x-show="!done"
                    @click="
                        if(!confirm('Move all incomplete tasks to tomorrow?')) return;
                        rolling = true;
                        fetch('{{ route('admin.api.tasks.rollover-today') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        })
                        .then(r => r.json())
                        .then(d => { rolling = false; done = true; message = d.message; setTimeout(() => window.location.reload(), 1500); })
                        .catch(() => { rolling = false; alert('Failed to roll over tasks.'); });
                    "
                    :disabled="rolling"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold text-white border-0 cursor-pointer transition-all hover:shadow-md shrink-0 flex items-center gap-2"
                    style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <svg x-show="!rolling" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    <svg x-show="rolling" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="rolling ? 'Rolling over...' : 'Roll Over to Tomorrow'"></span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 6 — Quick Add Drawer
    ═══════════════════════════════════════════════ --}}
    <div x-data="quickAdd()" @keydown.escape.window="open = false" @dayos:open-add-task.window="open = true">
        {{-- FAB (hidden on mobile — bottom nav has the add button) --}}
        <button @click="open = true" data-action="open-add-task"
            class="fixed bottom-7 right-7 w-[56px] h-[56px] rounded-[16px] border-0 cursor-pointer text-white hidden sm:flex items-center justify-center z-50 transition-all hover:scale-105 hover:shadow-2xl active:scale-95"
            style="background: var(--day-color); box-shadow: 0 8px 28px var(--day-shadow);">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        </button>

        {{-- Overlay --}}
        <div x-show="open" x-transition.opacity @click="open = false"
             class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm z-[60]"></div>

        {{-- Sheet --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl px-6 pt-5 pb-9 z-[70] max-w-[560px] mx-auto shadow-[0_-8px_40px_rgba(0,0,0,0.12)] max-h-[90vh] overflow-y-auto">

            {{-- Drag handle + Close button --}}
            <div class="flex items-center justify-between mb-4">
                <div class="w-9 h-1 rounded-sm bg-slate-200"></div>
                <button @click="open = false" type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 border-0 bg-transparent cursor-pointer transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Title input --}}
            <input type="text" x-model="title" x-ref="titleInput"
                   @keydown.enter="submit()"
                   placeholder="Task title..."
                   class="w-full text-lg font-semibold text-slate-800 py-3.5 border-0 border-b-2 border-slate-100 outline-none bg-transparent mb-5 tracking-tight placeholder:text-slate-300 placeholder:font-normal"
                   x-init="$watch('open', v => { if(v) setTimeout(()=>$refs.titleInput.focus(), 100) })">

            {{-- Pending Tasks from Database --}}
            @if($pendingTasks->count() > 0)
            <div class="mb-5" x-show="!title.trim()">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Pending tasks — tap to add to today
                </p>
                <div class="flex flex-wrap gap-2 max-h-[140px] overflow-y-auto">
                    @foreach($pendingTasks as $pt)
                    <button type="button"
                        @click="pullTask({ id: {{ $pt->id }} })"
                        class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-600 font-medium hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-all flex items-center gap-1.5"
                        :class="pulledIds.includes({{ $pt->id }}) && 'opacity-30 pointer-events-none'"
                        title="{{ $pt->pillar ? ucfirst($pt->pillar) : '' }}{{ $pt->estimated_minutes ? ' · '.$pt->estimated_minutes.'m' : '' }}">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $pt->priority === 'must' ? '#ef4444' : ($pt->priority === 'bonus' ? '#22c55e' : '#f59e0b') }};"></span>
                        {{ $pt->title }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Task Type Toggle --}}
            <div class="mb-5">
                <div class="flex gap-2 bg-slate-50 rounded-xl p-1">
                    <button @click="taskType = 'daily'" type="button"
                            class="flex-1 py-2.5 rounded-lg text-[12px] font-semibold border-0 cursor-pointer transition-all"
                            :class="taskType === 'daily' ? 'bg-white text-slate-800 shadow-sm' : 'bg-transparent text-slate-400'">
                        ⚡ Daily Task
                    </button>
                    <button @click="taskType = 'project'" type="button"
                            class="flex-1 py-2.5 rounded-lg text-[12px] font-semibold border-0 cursor-pointer transition-all"
                            :class="taskType === 'project' ? 'bg-white text-slate-800 shadow-sm' : 'bg-transparent text-slate-400'">
                        🗂️ Project Task
                    </button>
                </div>
            </div>

            {{-- Priority pills --}}
            <div class="mb-5">
                <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2.5">Priority</label>
                <div class="flex gap-2">
                    <template x-for="p in ['must','should','bonus']" :key="p">
                        <button @click="priority = p" type="button"
                                class="px-5 py-2.5 rounded-xl text-[13px] font-medium border cursor-pointer transition-all"
                                :style="priority === p ? 'background:' + priorityBg(p) + ';color:#fff;border-color:' + priorityBg(p) + ';box-shadow:0 2px 8px ' + priorityBg(p) + '40;' : 'background:transparent;color:#64748b;border-color:#e5e7eb;'"
                                x-text="p.charAt(0).toUpperCase() + p.slice(1)"></button>
                    </template>
                </div>
            </div>

            {{-- Impact Rating --}}
            <div class="mb-5">
                <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2.5">Impact Rating</label>
                <div class="flex gap-1.5 flex-wrap">
                    <template x-for="[val, dot, label] in [[4,'🔴','Critical'],[3,'🟠','High'],[2,'🟡','Medium'],[1,'🟢','Low'],[0,'⚪','Minimal']]" :key="val">
                        <button @click="impactRating = val" type="button"
                                class="flex items-center gap-1 px-3 py-1.5 rounded-full text-[11px] font-medium border cursor-pointer transition-all"
                                :style="impactRating === val ? 'background:#4338ca;color:#fff;border-color:#4338ca;' : 'background:transparent;color:#64748b;border-color:#e5e7eb;'">
                            <span x-text="dot"></span>
                            <span x-text="label"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Daily Task Fields --}}
            <div x-show="taskType === 'daily'" x-transition>
                {{-- Block + Pillar row --}}
                <div class="flex gap-3 mb-5">
                    <div class="flex-1">
                        <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Time Block</label>
                        <select x-model="blockId" class="w-full py-3 px-3.5 rounded-xl border border-slate-200 text-[13px] text-slate-800 bg-white outline-none appearance-none">
                            <option value="">Anytime</option>
                            @foreach($timeBlocks as $block)
                                <option value="{{ $block->id }}">{{ $block->name }} ({{ \Carbon\Carbon::parse($block->start_time)->format('H:i') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Pillar</label>
                        <select x-model="pillar" class="w-full py-3 px-3.5 rounded-xl border border-slate-200 text-[13px] text-slate-800 bg-white outline-none appearance-none">
                            <option value="">None</option>
                            @foreach($pillarList as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Estimated minutes --}}
                <div class="mb-6">
                    <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Estimated Minutes</label>
                    <input type="number" x-model="minutes" min="0" max="127" placeholder="e.g. 30" class="w-[100px] py-3 px-3.5 rounded-xl border border-slate-200 text-[13px] text-slate-800 outline-none">
                </div>
            </div>

            {{-- Project Task Fields --}}
            <div x-show="taskType === 'project'" x-transition class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Pillar</label>
                    <select x-model="pillar" class="w-full py-3 px-3.5 rounded-xl border border-slate-200 text-[13px] text-slate-800 bg-white outline-none appearance-none">
                        <option value="">None</option>
                        @foreach($pillarList as $p)
                            <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Start Date</label>
                        <input type="date" x-model="startDate" class="w-full py-3 px-3.5 rounded-xl border border-slate-200 text-[13px] text-slate-800 outline-none bg-white">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Deadline <span class="text-red-400">*</span></label>
                        <input type="datetime-local" x-model="deadlineAt" class="w-full py-3 px-3.5 rounded-xl border border-slate-200 text-[13px] text-slate-800 outline-none bg-white">
                    </div>
                </div>
                <p x-show="deadlineAt" class="text-[11px] text-slate-400 -mt-2 px-1" x-text="deadlinePreview"></p>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Deadline Notes <span class="text-slate-300">(optional)</span></label>
                    <input type="text" x-model="deadlineNotes" placeholder="e.g. Client presentation, launch day" class="w-full py-3 px-3.5 rounded-xl border border-slate-200 text-[13px] text-slate-800 outline-none bg-white placeholder:text-slate-300">
                </div>
            </div>

            {{-- Submit --}}
            <button @click="submit()" :disabled="!title.trim()"
                    class="w-full py-4 rounded-[14px] border-0 cursor-pointer text-[15px] font-semibold text-white transition-all appearance-none disabled:opacity-40 disabled:cursor-default disabled:shadow-none"
                    style="background: {{ $hex }}; box-shadow: 0 4px 16px {{ $hex }}30;">
                Add Task
            </button>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     NUDGE SYSTEM — Fixed bottom-left
═══════════════════════════════════════════════ --}}
@if($hasNudges)
<div class="fixed bottom-6 left-6 z-50 flex flex-col-reverse gap-3 max-w-[320px] sm:max-w-[320px] max-sm:left-4 max-sm:right-4 max-sm:max-w-[calc(100%-2rem)]"
     x-data="nudgeSystem(@js($nudges))" x-init="nudges.forEach(n => startAutoDismiss(n))">
    <template x-for="nudge in nudges" :key="nudge.context_key">
        <div :class="nudge.exiting ? 'nudge-exit' : 'nudge-enter'"
             class="bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden relative"
             :style="'border-left: 3px solid ' + nudge.hex_color">
            {{-- Top row --}}
            <div class="flex items-start gap-2.5 px-4 pt-3 pb-1">
                <span class="text-xl leading-none shrink-0" x-text="nudge.icon_emoji"></span>
                <p class="flex-1 text-sm font-semibold text-slate-800 leading-tight" x-text="nudge.title"></p>
                <button x-show="nudge.is_dismissible" @click="dismiss(nudge)"
                    class="text-slate-300 hover:text-slate-500 text-base leading-none cursor-pointer p-0.5 -mt-0.5 transition-colors">✕</button>
            </div>
            {{-- Body --}}
            <div class="px-4 pb-2">
                <p class="text-xs text-slate-500 leading-relaxed" x-text="nudge.message"></p>
            </div>
            {{-- CTA --}}
            <div x-show="nudge.cta_label" class="px-4 pb-3">
                <button @click="clickCta(nudge)"
                    class="px-3 py-1.5 rounded-lg text-[11px] font-semibold text-white cursor-pointer border-0 transition-opacity hover:opacity-90"
                    :style="'background:' + nudge.hex_color"
                    x-text="nudge.cta_label"></button>
            </div>
            {{-- Auto-dismiss progress bar --}}
            <div x-show="nudge.auto_dismiss_seconds" class="absolute bottom-0 left-0 right-0">
                <div class="nudge-progress" :style="'animation-duration:' + nudge.auto_dismiss_seconds + 's; background:' + nudge.hex_color"></div>
            </div>
        </div>
    </template>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     AI OVERLOAD WARNING — Nudge-style card
═══════════════════════════════════════════════ --}}
@if(!empty($overloadWarning) && ($overloadWarning['overloaded'] ?? false))
<div class="fixed bottom-6 right-6 z-50 max-w-[340px] max-sm:right-4 max-sm:left-4 max-sm:max-w-[calc(100%-2rem)]"
     x-data="{ show: true }" x-show="show" x-transition>
    <div class="bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden nudge-enter" style="border-left: 3px solid #e67e22;">
        <div class="flex items-start gap-2.5 px-4 pt-3 pb-1">
            <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="flex-1 text-sm font-semibold text-slate-800 leading-tight">Overloaded Day Detected</p>
            <button @click="show = false" class="text-slate-300 hover:text-slate-500 leading-none cursor-pointer p-1 -mt-0.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-4 pb-2">
            <p class="text-xs text-slate-500 leading-relaxed">{{ $overloadWarning['message'] ?? 'You have too many tasks today.' }}</p>
        </div>
        @if(!empty($overloadWarning['auto_deferred']))
        <div class="px-4 pb-2">
            <p class="text-[10px] font-bold text-green-700 uppercase tracking-wider mb-1.5">✅ Auto-deferred (far-off deadlines)</p>
            <div class="space-y-1">
                @foreach($overloadWarning['auto_deferred'] as $ad)
                <div class="flex items-center justify-between gap-2" x-data="{ undone: false }" x-show="!undone">
                    <span class="text-xs text-slate-600 truncate flex-1">{{ $ad['title'] }} <span class="text-slate-400">· due {{ $ad['due_date'] }} ({{ $ad['days_out'] }}d)</span></span>
                    <button @click="fetch('{{ url('admin/api/tasks') }}/{{ $ad['id'] }}/status', { method: 'PATCH', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: JSON.stringify({status:'backlog'}) }).then(() => { undone = true; window.location.reload(); })"
                        class="text-[10px] font-semibold text-indigo-600 hover:text-indigo-800 cursor-pointer whitespace-nowrap border-0 bg-transparent">Undo ↩</button>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if(!empty($overloadWarning['defer_suggestions']))
        <div class="px-4 pb-3 space-y-1.5">
            <p class="text-[10px] font-bold text-orange-700 uppercase tracking-wider mb-1">💡 AI suggests deferring</p>
            @foreach($overloadWarning['defer_suggestions'] as $deferTitle)
                @php $deferTask = $plan->tasks()->where('title', $deferTitle)->whereIn('status', ['backlog', 'wip'])->first(); @endphp
                @if($deferTask)
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs text-slate-600 truncate">{{ $deferTitle }}</span>
                    <button onclick="fetch('{{ route('admin.api.tasks.defer', $deferTask->id) }}', { method: 'POST', headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'} }).then(() => this.closest('div').remove())"
                        class="text-[10px] font-semibold text-orange-600 hover:text-orange-800 cursor-pointer whitespace-nowrap border-0 bg-transparent">Defer →</button>
                </div>
                @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif

@push('scripts')
<script>
    // Force reload when Chrome restores page from bfcache
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) window.location.reload();
    });

    const CSRF = '{{ csrf_token() }}';
    const PLAN_ID = {{ $plan->id }};
    const API = {
        base: '{{ url("admin/api/tasks") }}',
        store: '{{ route("admin.api.tasks.store") }}',
        complete: id => '{{ url("admin/api/tasks") }}/' + id + '/complete',
        defer: id => '{{ url("admin/api/tasks") }}/' + id + '/defer',
        destroy: id => '{{ url("admin/api/tasks") }}/' + id,
        reorder: '{{ route("admin.api.tasks.reorder") }}',
        cycleStatus: id => '{{ url("admin/api/tasks") }}/' + id + '/cycle-status',
        updateStatus: id => '{{ url("admin/api/tasks") }}/' + id + '/status',
    };

    const PILLAR_MAP = @js($pillarColors);

    const STATUS_CONFIG = {
        backlog:  { label: 'Backlog',  color: '#7a7974', bg: '#7a797422', emoji: '📋' },
        wip:      { label: 'WIP',      color: '#006494', bg: '#00649422', emoji: '⚡' },
        done:     { label: 'Done',     color: '#437a22', bg: '#437a2222', emoji: '✅' },
        deferred: { label: 'Deferred', color: '#964219', bg: '#96421922', emoji: '⏭️' },
    };
    const STATUS_CYCLE = { backlog: 'wip', wip: 'done', done: 'backlog', deferred: 'backlog' };

    function tbcbPill(task) {
        return {
            open: false,
            isOverdue(dateStr) {
                if (!dateStr) return false;
                return new Date(dateStr.substring(0, 10) + 'T23:59:59') < new Date();
            },
            fmtDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr.substring(0, 10) + 'T00:00:00');
                return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
            },
            async setTbcb(task, val) {
                const res = await fetch(API.base + '/' + task.id, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ due_date: val || null })
                });
                if (res.ok) {
                    task.due_date = val || null;
                    this.open = false;
                }
            }
        };
    }

    function taskList() {
        return {
            groups: @js($groupedTasks),
            sortables: [],
            actionTask: null,

            init() {
                this.$nextTick(() => this.initSortable());
            },

            initSortable() {
                const self = this;
                document.querySelectorAll('.task-group').forEach(el => {
                    Sortable.create(el, {
                        group: 'tasks',
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'opacity-30',
                        delay: 150,
                        delayOnTouchOnly: true,
                        touchStartThreshold: 5,
                        forceFallback: true,
                        fallbackClass: 'opacity-50',
                        onEnd: (evt) => {
                            const fromBlock = evt.from.dataset.blockId;
                            const toBlock = evt.to.dataset.blockId;
                            const taskId = parseInt(evt.item.dataset.taskId);

                            if (fromBlock !== toBlock) {
                                const task = (self.groups[fromBlock] || []).find(t => t.id === taskId);
                                if (task) {
                                    self.groups[fromBlock] = (self.groups[fromBlock] || []).filter(t => t.id !== taskId);
                                    task.time_block_id = toBlock === 'anytime' ? null : parseInt(toBlock);
                                    if (!self.groups[toBlock]) self.groups[toBlock] = [];
                                    self.groups[toBlock].splice(evt.newIndex, 0, task);
                                }
                            }

                            const newBlockId = toBlock === 'anytime' ? null : parseInt(toBlock);
                            const items = [];
                            evt.to.querySelectorAll('[data-task-id]').forEach((row, i) => {
                                items.push({ id: parseInt(row.dataset.taskId), sort_order: i, time_block_id: newBlockId });
                            });
                            if (fromBlock !== toBlock) {
                                const oldBlockId = fromBlock === 'anytime' ? null : parseInt(fromBlock);
                                evt.from.querySelectorAll('[data-task-id]').forEach((row, i) => {
                                    items.push({ id: parseInt(row.dataset.taskId), sort_order: i, time_block_id: oldBlockId });
                                });
                            }
                            fetch(API.reorder, {
                                method: 'POST',
                                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                                body: JSON.stringify({ items })
                            });
                        }
                    });
                });
            },

            sortedGroup(blockId) {
                const tasks = this.groups[blockId] || [];
                const order = { wip: 0, backlog: 1, deferred: 2, done: 3 };
                return [...tasks].sort((a,b) => {
                    const sa = order[a.status] ?? 1, sb = order[b.status] ?? 1;
                    if (sa !== sb) return sa - sb;
                    return (a.sort_order || 0) - (b.sort_order || 0);
                });
            },

            priorityColor(p) {
                return p === 'must' ? '#ef4444' : p === 'bonus' ? '#22c55e' : '#f59e0b';
            },

            pillarClasses(pillar) {
                const color = PILLAR_MAP[pillar] || 'gray';
                const map = {
                    yellow:'bg-yellow-100 text-yellow-700 border border-yellow-200',
                    gray:'bg-gray-100 text-gray-600 border border-gray-200',
                    orange:'bg-orange-100 text-orange-700 border border-orange-200',
                    lime:'bg-lime-100 text-lime-700 border border-lime-200',
                    sky:'bg-sky-100 text-sky-700 border border-sky-200',
                    purple:'bg-purple-100 text-purple-700 border border-purple-200',
                    indigo:'bg-indigo-100 text-indigo-700 border border-indigo-200',
                    blue:'bg-blue-100 text-blue-700 border border-blue-200',
                    teal:'bg-teal-100 text-teal-700 border border-teal-200',
                    pink:'bg-pink-100 text-pink-700 border border-pink-200',
                    rose:'bg-rose-100 text-rose-700 border border-rose-200',
                    green:'bg-green-100 text-green-700 border border-green-200',
                    red:'bg-red-100 text-red-600 border border-red-200',
                    emerald:'bg-emerald-100 text-emerald-700 border border-emerald-200',
                    violet:'bg-violet-100 text-violet-700 border border-violet-200',
                };
                return map[color] || map.gray;
            },

            openTaskAction(task) {
                this.actionTask = task;
            },

            async setTaskStatus(task, newStatus) {
                if (!task || task.status === newStatus) return;
                const oldStatus = task.status || 'backlog';
                const wasDone = oldStatus === 'done';
                const nowDone = newStatus === 'done';
                task.status = newStatus;
                if (nowDone && !wasDone) this.updateStats(1, 0);
                if (wasDone && !nowDone) this.updateStats(-1, 0);
                await fetch(API.updateStatus(task.id), {
                    method: 'PATCH',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                    body: JSON.stringify({ status: newStatus })
                });
                if (window.DayOSSync) window.DayOSSync.refresh();
            },

            async setTaskPriority(task, newPriority) {
                if (!task || task.priority === newPriority) return;
                task.priority = newPriority;
                await fetch('{{ url("admin/api/tasks") }}/' + task.id, {
                    method: 'PATCH',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                    body: JSON.stringify({ priority: newPriority })
                });
            },

            async cycleTaskStatus(task) {
                const oldStatus = task.status || 'backlog';
                const newStatus = STATUS_CYCLE[oldStatus] || 'backlog';
                task.status = newStatus;
                const wasDone = oldStatus === 'done';
                const nowDone = newStatus === 'done';
                if (nowDone && !wasDone) this.updateStats(1, 0);
                if (wasDone && !nowDone) this.updateStats(-1, 0);
                await fetch(API.cycleStatus(task.id), {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                });
            },

            async completeTask(task) {
                if (task.status === 'done') return;
                task.status = 'done';
                this.updateStats(1, 0);
                await fetch(API.complete(task.id), {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                });
                if (window.DayOSSync) window.DayOSSync.refresh();
            },

            async deferTask(task) {
                const blockKey = task.time_block_id ? String(task.time_block_id) : 'anytime';
                this.groups[blockKey] = (this.groups[blockKey] || []).filter(t => t.id !== task.id);
                this.updateStats(task.status === 'done' ? -1 : 0, -1);
                await fetch(API.defer(task.id), {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                });
                if (window.DayOSSync) window.DayOSSync.refresh();
            },

            async deleteTask(task) {
                const blockKey = task.time_block_id ? String(task.time_block_id) : 'anytime';
                this.groups[blockKey] = (this.groups[blockKey] || []).filter(t => t.id !== task.id);
                this.updateStats(task.status === 'done' ? -1 : 0, -1);
                await fetch(API.destroy(task.id), {
                    method: 'DELETE',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                });
                if (window.DayOSSync) window.DayOSSync.refresh();
            },

            updateStats(completedDelta, totalDelta) {
                const stats = document.querySelector('[x-data*="completed"]');
                if (!stats) return;
                const sd = Alpine.$data(stats);
                if (!sd) return;
                sd.completed = Math.max(0, sd.completed + completedDelta);
                sd.total = Math.max(0, sd.total + totalDelta);
                sd.pct = sd.total > 0 ? Math.round((sd.completed / sd.total) * 100) : 0;
                const bar = stats.querySelector('[x-ref="bar"]');
                if (bar) bar.style.width = sd.pct + '%';
            },

            scoreBadgeBg(score) {
                if (score >= 75) return '#a1354422';
                if (score >= 55) return '#da710122';
                if (score >= 35) return '#d1990022';
                return '#7a797422';
            },

            scoreBadgeColor(score) {
                if (score >= 75) return '#a13544';
                if (score >= 55) return '#da7101';
                if (score >= 35) return '#d19900';
                return '#7a7974';
            },

            // Plan My Day state
            planning: false,
            planModalOpen: false,
            planTasks: [],
            planRationale: '',
            resurfaceCandidates: [],
            selectedResurface: [],

            async planDay() {
                this.planning = true;
                try {
                    const res = await fetch('{{ route("admin.api.tasks.plan-day") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
                    });
                    const data = await res.json();
                    this.planTasks = data.tasks || [];
                    this.planRationale = data.rationale || '';
                    this.resurfaceCandidates = data.resurface_candidates || [];
                    this.selectedResurface = [];
                    this.planModalOpen = true;
                } catch(e) { console.error(e); }
                finally { this.planning = false; }
            },

            async confirmPlan() {
                const taskOrder = this.planTasks.map(t => t.id);
                await fetch('{{ route("admin.api.tasks.confirm-plan") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ task_order: taskOrder, resurface_task_ids: this.selectedResurface })
                });
                this.planModalOpen = false;
                window.location.reload();
            },

            async autoSort() {
                await fetch('{{ route("admin.api.tasks.auto-sort") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF }
                });
                window.location.reload();
            },

            addTask(task) {
                const key = task.time_block_id ? String(task.time_block_id) : 'anytime';
                if (!this.groups[key]) this.groups[key] = [];
                this.groups[key].push(task);
                this.updateStats(0, 1);
                this.$nextTick(() => this.initSortable());
            }
        };
    }

    function upskillWidget() {
        return {
            sessionActive: {{ $activeSession ? 'true' : 'false' }},
            sessionId: {{ $activeSession?->id ?? 'null' }},
            sessionNotes: '{{ addslashes($activeSession?->notes ?? '') }}',
            startedAt: {{ $activeSession ? "'" . $activeSession->started_at->toIso8601String() . "'" : 'null' }},
            elapsedSeconds: 0,
            timerDisplay: '00:00',
            timerInterval: null,
            starting: false,
            ending: false,
            showEndForm: false,
            endDuration: '',
            endTakeaway: '',

            init() {
                if (this.sessionActive && this.startedAt) {
                    this.startTimer();
                }
            },

            startTimer() {
                const start = new Date(this.startedAt).getTime();
                this.timerInterval = setInterval(() => {
                    this.elapsedSeconds = Math.floor((Date.now() - start) / 1000);
                    const m = Math.floor(this.elapsedSeconds / 60);
                    const s = this.elapsedSeconds % 60;
                    this.timerDisplay = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
                }, 1000);
            },

            async startSession() {
                this.starting = true;
                const res = await fetch('{{ route("admin.api.upskilling.sessions.start") }}', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                    body: JSON.stringify({ learning_item_id: {{ $upskillSuggestion?->id ?? 'null' }} })
                });
                if (res.ok) {
                    const session = await res.json();
                    this.sessionId = session.id;
                    this.startedAt = session.started_at;
                    this.sessionActive = true;
                    this.startTimer();
                }
                this.starting = false;
            },

            async saveNotes() {
                if (!this.sessionId) return;
                await fetch('{{ url("admin/api/upskilling/sessions") }}/' + this.sessionId + '/notes', {
                    method: 'PATCH',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                    body: JSON.stringify({ notes: this.sessionNotes })
                });
            },

            async endSession() {
                this.ending = true;
                const body = {};
                if (this.endDuration) body.duration_minutes = parseInt(this.endDuration);
                if (this.endTakeaway) body.takeaway = this.endTakeaway;
                if (this.sessionNotes) body.notes = this.sessionNotes;

                await fetch('{{ url("admin/api/upskilling/sessions") }}/' + this.sessionId + '/end', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                    body: JSON.stringify(body)
                });

                if (this.timerInterval) clearInterval(this.timerInterval);
                this.sessionActive = false;
                this.ending = false;
                location.reload();
            }
        };
    }

    function quickAdd() {
        return {
            open: false,
            title: '',
            priority: 'should',
            impactRating: 2,
            blockId: '',
            pillar: '',
            minutes: '',
            taskType: 'daily',
            startDate: new Date().toISOString().split('T')[0],
            deadlineAt: '',
            deadlineNotes: '',
            pulledIds: [],

            get deadlinePreview() {
                if (!this.deadlineAt) return '';
                const d = new Date(this.deadlineAt);
                const diff = Math.round((d - new Date()) / 86400000);
                if (diff < 0)  return '⚠️ This date is in the past';
                if (diff === 0) return '📅 Due today';
                if (diff === 1) return '📅 Due tomorrow';
                return '📅 Due in ' + diff + ' days — ' + d.toLocaleDateString('en-IN', { weekday:'long', day:'numeric', month:'long' }) + ' at ' + d.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' });
            },

            priorityBg(p) {
                return p === 'must' ? '#ef4444' : p === 'bonus' ? '#22c55e' : '#f59e0b';
            },

            async pullTask(task) {
                if (this.pulledIds.includes(task.id)) return;
                this.pulledIds.push(task.id);
                const res = await fetch('{{ url("admin/api/tasks") }}/' + task.id + '/pull-today', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const pulled = await res.json();
                    const tl = document.querySelector('[x-data="taskList()"]');
                    if (tl) {
                        const data = Alpine.$data(tl);
                        if (data && data.addTask) data.addTask(pulled);
                    }
                }
            },

            async submit() {
                if (!this.title.trim()) return;
                const body = {
                    daily_plan_id: PLAN_ID,
                    title: this.title.trim(),
                    priority: this.priority,
                    impact_rating: this.impactRating,
                    pillar: this.pillar || null,
                    task_type: this.taskType,
                };
                if (this.taskType === 'daily') {
                    body.time_block_id = this.blockId || null;
                    body.estimated_minutes = this.minutes ? parseInt(this.minutes) : null;
                } else {
                    body.start_date = this.startDate || null;
                    body.deadline_at = this.deadlineAt || null;
                    body.deadline_notes = this.deadlineNotes || null;
                }
                const res = await fetch(API.store, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF,'Accept':'application/json'},
                    body: JSON.stringify(body)
                });
                if (res.ok) {
                    const task = await res.json();
                    if (this.taskType === 'daily') {
                        const tl = document.querySelector('[x-data="taskList()"]');
                        if (tl) {
                            const data = Alpine.$data(tl);
                            if (data && data.addTask) data.addTask(task);
                        }
                    } else {
                        // Reload to show project task in the project tasks section
                        window.location.reload();
                    }
                    this.title = '';
                    this.priority = 'should';
                    this.impactRating = 2;
                    this.blockId = '';
                    this.pillar = '';
                    this.minutes = '';
                    this.taskType = 'daily';
                    this.startDate = new Date().toISOString().split('T')[0];
                    this.deadlineAt = '';
                    this.deadlineNotes = '';
                    this.open = false;
                }
            }
        };
    }

    function nudgeSystem(initialNudges) {
        return {
            nudges: initialNudges.map(n => ({ ...n, exiting: false })),
            async dismiss(nudge) {
                nudge.exiting = true;
                await fetch('{{ route("admin.api.nudges.dismiss") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ nudge_type: nudge.type, context_key: nudge.context_key })
                });
                setTimeout(() => { this.nudges = this.nudges.filter(n => n.context_key !== nudge.context_key); }, 300);
            },
            async clickCta(nudge) {
                await fetch('{{ route("admin.api.nudges.click") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ nudge_type: nudge.type, context_key: nudge.context_key })
                });
                window.location.href = nudge.cta_url;
            },
            startAutoDismiss(nudge) {
                if (!nudge.auto_dismiss_seconds) return;
                setTimeout(() => this.dismiss(nudge), nudge.auto_dismiss_seconds * 1000);
            }
        };
    }

    // ─── PWA Install Banner ───
    function pwaInstall() {
        return {
            showBanner: false,
            deferredPrompt: null,
            iosDevice: /iPhone|iPad|iPod/.test(navigator.userAgent),
            init() {
                if (window.matchMedia('(display-mode: standalone)').matches) return;
                if (this.iosDevice) {
                    if (!window._pwaBannerDismissed) this.showBanner = true;
                    return;
                }
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    if (!window._pwaBannerDismissed) this.showBanner = true;
                });
            },
            async install() {
                if (!this.deferredPrompt) return;
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                this.showBanner = false;
                this.deferredPrompt = null;
            },
            dismiss() {
                this.showBanner = false;
                window._pwaBannerDismissed = true;
            }
        };
    }

    // ─── Close task action popup on Escape ───
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const tl = document.querySelector('[x-data="taskList()"]');
            if (tl) { const d = Alpine.$data(tl); if (d) d.actionTask = null; }
        }
    });

    // Listen for dayos:open-add-task event from bottom nav
    document.addEventListener('dayos:open-add-task', () => {
        const qa = document.querySelector('[x-data="quickAdd()"]');
        if (qa) { Alpine.$data(qa).open = true; }
    });

    // ─── Sync Indicator Alpine Component ───
    function syncIndicator() {
        return {
            lastSyncedAt: null,
            syncPulse: false,
            init() {
                // Listen for sync events
                window.addEventListener('dayos:synced', (e) => {
                    this.lastSyncedAt = new Date().toLocaleTimeString('en-IN', {
                        hour: '2-digit', minute: '2-digit'
                    });
                    this.syncPulse = true;
                    setTimeout(() => this.syncPulse = false, 2000);
                });
            },
            manualRefresh() {
                if (window.DayOSSync) window.DayOSSync.refresh();
            }
        };
    }
</script>
<script src="/js/dayos-sync.js"></script>
<script>
    // ─── DayOS Sync Initialization ───
    document.addEventListener('DOMContentLoaded', () => {
        DayOSSync.init((data, meta) => {
            // Update Alpine taskList state if component is mounted
            const tl = document.querySelector('[x-data="taskList()"]');
            if (tl) {
                const dashboard = Alpine.$data(tl);
                if (dashboard && data.planned) {
                    // Rebuild groups from fresh data
                    const allTasks = [
                        ...(data.planned || []),
                        ...(data.floating || []),
                        ...(data.tbcb || []),
                    ];
                    // Re-group by time_block_id
                    const newGroups = {};
                    allTasks.forEach(task => {
                        const key = task.time_block_id ? String(task.time_block_id) : 'anytime';
                        if (!newGroups[key]) newGroups[key] = [];
                        newGroups[key].push(task);
                    });
                    // Preserve existing block keys
                    Object.keys(dashboard.groups).forEach(key => {
                        if (!newGroups[key]) newGroups[key] = [];
                    });
                    dashboard.groups = newGroups;
                }
            }

            // Dispatch sync event for indicator
            if (meta.source === 'network') {
                window.dispatchEvent(new CustomEvent('dayos:synced'));
            }
        });

        // Expose globally for task action handlers
        window.DayOSSync = DayOSSync;
    });
</script>
@endpush
@endsection
