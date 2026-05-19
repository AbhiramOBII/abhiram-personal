@extends('admin.layouts.app')

@section('title', 'Today — ' . ($workingDay?->day_name ?? 'DayOS'))

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
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
         SECTION 1 — Day Theme Header
    ═══════════════════════════════════════════════ --}}
    <div class="today-header rounded-[18px] sm:p-7 p-5 mb-6 sm:mb-6 relative overflow-hidden" style="border: 1px solid {{ $hex }}20; background: {{ $hex }}08;">
        <div class="relative flex items-start justify-between flex-wrap gap-3.5">
            <div class="flex items-center gap-4">
                <span class="text-[44px] leading-none drop-shadow-sm">{{ $workingDay?->icon_emoji ?? '📅' }}</span>
                <div>
                    <h1 class="font-['Space_Grotesk'] text-2xl font-bold text-slate-800 tracking-tight">{{ $workingDay?->day_name ?? 'Today' }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $workingDay?->theme ?? '' }}</p>
                    @if($workingDay)
                        <span class="inline-block mt-2 px-3 py-1 rounded-full text-[11px] font-semibold" style="background: {{ $hex }}15; color: {{ $hex }}; border: 1px solid {{ $hex }}25;">{{ $workingDay->energyLabel() }}</span>
                    @endif
                </div>
            </div>
            <div class="text-right pt-1">
                <p class="text-sm font-semibold text-slate-800">{{ now()->format('l') }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ now()->format('j F Y') }}</p>
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
            <div class="py-4 sm:py-[18px] px-3 sm:px-4 text-center rounded-xl bg-white border border-slate-100">
                <p class="text-2xl font-bold tracking-tight" style="color: {{ $hex }};" x-text="completed + '/' + total">{{ $completed }}/{{ $total }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-widest font-semibold">Done</p>
            </div>
            <div class="py-4 sm:py-[18px] px-3 sm:px-4 text-center rounded-xl bg-white border border-slate-100">
                <p class="text-2xl font-bold text-slate-800 tracking-tight" x-text="total - completed">{{ $total - $completed }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-widest font-semibold">Pending</p>
            </div>
            <div class="py-4 sm:py-[18px] px-3 sm:px-4 text-center rounded-xl bg-white border border-slate-100">
                <p class="text-2xl font-bold text-amber-500 tracking-tight" x-text="rolledOver">{{ $rolledOver }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-widest font-semibold">Rolled Over</p>
            </div>
        </div>
        <div class="w-full h-[5px] rounded bg-slate-100 overflow-hidden">
            <div x-ref="bar" class="h-full rounded transition-all duration-[1200ms]" style="background: linear-gradient(90deg, {{ $hex }}, {{ $hex }}cc); width: 0%;"
                 x-init="setTimeout(() => $refs.bar.style.width = pct + '%', 200)"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 4a — Sunday Vision Mode Banner
    ═══════════════════════════════════════════════ --}}
    @if($isSundayVisionMode)
    <div class="mb-7 rounded-2xl p-5 border border-teal-200 bg-teal-50/60">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold text-teal-800">🌅 Today is Vision Day</p>
                <p class="text-xs text-teal-600 mt-0.5">Your weekly review is waiting — reflect, reset, plan.</p>
            </div>
            <a href="{{ route('admin.weekly-review.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-white no-underline transition-all hover:opacity-90 shrink-0" style="background: #4f98a3;">
                Open Review →
            </a>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 4ai — AI Daily Briefing
    ═══════════════════════════════════════════════ --}}
    @if(!empty($aiBriefing))
    <div class="mb-7 rounded-2xl bg-white border border-slate-200 p-4 relative overflow-hidden" style="border-left: 3px solid {{ $hex }};">
        <div class="flex items-start gap-3">
            <span class="text-lg leading-none mt-0.5">✨</span>
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold mb-1.5">Your Briefing</p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $aiBriefing }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 4b — Today's Practices
    ═══════════════════════════════════════════════ --}}
    @if($practiceLogs->count())
    @php
        $practicesDone = $practiceLogs->where('is_completed', true)->count();
        $practicesTotal = $practiceLogs->count();
        // Group: stacked practices indented under their trigger
        $topLevel = $practiceLogs->filter(fn($l) => !$l->practice->stack_after_practice_id);
        $stacked = $practiceLogs->filter(fn($l) => $l->practice->stack_after_practice_id)->groupBy(fn($l) => $l->practice->stack_after_practice_id);
    @endphp
    <div class="mb-7">
        <div class="flex items-center justify-between mb-3 px-1">
            <span class="text-[13px] font-bold text-slate-800 tracking-tight">Today's Practices</span>
            <span class="px-3 py-0.5 rounded-xl text-[11px] font-semibold" style="background: {{ $hex }}10; color: {{ $hex }}; border: 1px solid {{ $hex }}20;">{{ $practicesDone }} / {{ $practicesTotal }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow overflow-hidden">
            @foreach($topLevel as $log)
                @php $p = $log->practice; $streak = $p->currentStreak(); @endphp
                <div x-data="{ completed: {{ $log->is_completed ? 'true' : 'false' }}, twoMin: {{ $log->used_two_minute_version ? 'true' : 'false' }}, loading: false, showNote: {{ $log->is_completed ? 'true' : 'false' }}, note: '{{ addslashes($log->note ?? '') }}', noteSaved: false }">
                    <div class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100 last:border-b-0 transition-opacity" :class="completed && 'opacity-50'">
                        <span class="text-[22px] leading-none shrink-0">{{ $p->icon_emoji }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-sm font-medium text-slate-800" :class="completed && 'line-through !text-slate-400'">{{ $p->name }}</span>
                                @if($streak >= 3)<span class="text-xs inline-flex items-center gap-0.5 bg-orange-50 px-1.5 py-px rounded-md font-semibold text-orange-600">🔥 {{ $streak }}</span>@endif
                                <template x-if="twoMin"><span class="px-2 py-px rounded-md text-[9px] font-bold bg-amber-100 text-amber-800">2-min</span></template>
                            </div>
                            @if($p->cue)<p class="text-[11px] text-slate-400 mt-0.5">{{ $p->cue }}</p>@endif
                            <button x-show="!showNote" @click="showNote = true" type="button" class="inline-flex items-center gap-1 mt-0.5 p-0 border-0 bg-transparent cursor-pointer text-[11px] text-slate-400 hover:text-slate-500 transition-colors">📝 note</button>
                        </div>
                        @if($p->is_two_minute_enabled)
                        <button x-show="!completed" @click="loading=true; fetch('{{ url('admin/api/practices') }}/{{ $p->id }}/complete', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: JSON.stringify({two_minute:true}) }).then(()=>{ completed=true; twoMin=true; showNote=true; loading=false; })"
                                class="px-3 py-1.5 rounded-lg border border-amber-400 bg-amber-50 text-amber-800 text-[11px] font-semibold cursor-pointer whitespace-nowrap transition-all hover:bg-amber-100 disabled:opacity-50" :disabled="loading">2 min</button>
                        @endif
                        <button @click="
                            loading=true;
                            if(completed){
                                fetch('{{ url('admin/api/practices') }}/{{ $p->id }}/uncomplete',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>{completed=false;twoMin=false;loading=false;});
                            } else {
                                fetch('{{ url('admin/api/practices') }}/{{ $p->id }}/complete',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>{completed=true;showNote=true;loading=false;});
                            }"
                            :disabled="loading"
                            class="w-[30px] h-[30px] rounded-full cursor-pointer flex items-center justify-center transition-all shrink-0"
                            style="border: 2px solid {{ $p->hex_color }};"
                            :style="completed ? 'background: {{ $p->hex_color }}; border-color: {{ $p->hex_color }}; box-shadow: 0 2px 8px {{ $p->hex_color }}40;' : 'background: transparent;'">
                            <svg x-show="completed" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                    <div x-show="showNote" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="px-4 pb-3 pl-[52px] relative">
                        <textarea x-model="note"
                            @blur="fetch('{{ url('admin/api/practices/logs') }}/{{ $log->id }}/note', { method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: JSON.stringify({note: note}) }).then(()=>{ noteSaved=true; setTimeout(()=>noteSaved=false, 1500); })"
                            placeholder="How did this go? Any thoughts, friction, or wins worth remembering..."
                            rows="2"
                            class="w-full px-2.5 py-2 rounded-lg border border-slate-200 text-xs text-slate-600 bg-slate-50 resize-y outline-none font-[inherit] transition-colors focus:border-slate-400"></textarea>
                        <span x-show="noteSaved" x-transition.opacity class="absolute right-5 bottom-4 text-[10px] text-green-600 font-semibold">Saved</span>
                    </div>
                </div>

                @foreach(($stacked[$p->id] ?? collect()) as $sLog)
                    @php $sp = $sLog->practice; $sStreak = $sp->currentStreak(); @endphp
                    <div x-data="{ completed: {{ $sLog->is_completed ? 'true' : 'false' }}, twoMin: {{ $sLog->used_two_minute_version ? 'true' : 'false' }}, loading: false, showNote: {{ $sLog->is_completed ? 'true' : 'false' }}, note: '{{ addslashes($sLog->note ?? '') }}', noteSaved: false }">
                        <div class="flex items-center gap-3 pl-12 pr-4 py-3.5 bg-slate-50/60 border-b border-slate-100 last:border-b-0 transition-opacity" :class="completed && 'opacity-50'">
                            <span class="text-lg leading-none shrink-0">{{ $sp->icon_emoji }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[13px] font-medium text-slate-600" :class="completed && 'line-through !text-slate-400'">{{ $sp->name }}</span>
                                    @if($sStreak >= 3)<span class="text-[11px] bg-orange-50 px-1.5 py-px rounded font-semibold text-orange-600">🔥 {{ $sStreak }}</span>@endif
                                    <template x-if="twoMin"><span class="px-1.5 py-px rounded text-[9px] font-bold bg-amber-100 text-amber-800">2-min</span></template>
                                </div>
                                @if($sp->stack_trigger)<p class="text-[10px] text-slate-400 mt-0.5">↳ {{ $sp->stack_trigger }}</p>@endif
                                <button x-show="!showNote" @click="showNote = true" type="button" class="inline-flex items-center gap-1 mt-0.5 p-0 border-0 bg-transparent cursor-pointer text-[10px] text-slate-400 hover:text-slate-500 transition-colors">📝 note</button>
                            </div>
                            @if($sp->is_two_minute_enabled)
                            <button x-show="!completed" @click="loading=true; fetch('{{ url('admin/api/practices') }}/{{ $sp->id }}/complete',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({two_minute:true})}).then(()=>{completed=true;twoMin=true;showNote=true;loading=false;})"
                                    class="px-2.5 py-1 rounded-lg border border-amber-400 bg-amber-50 text-amber-800 text-[10px] font-semibold cursor-pointer whitespace-nowrap hover:bg-amber-100 disabled:opacity-50" :disabled="loading">2 min</button>
                            @endif
                            <button @click="loading=true; if(completed){ fetch('{{ url('admin/api/practices') }}/{{ $sp->id }}/uncomplete',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>{completed=false;twoMin=false;loading=false;}); } else { fetch('{{ url('admin/api/practices') }}/{{ $sp->id }}/complete',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>{completed=true;showNote=true;loading=false;}); }"
                                :disabled="loading"
                                class="w-[26px] h-[26px] rounded-full cursor-pointer flex items-center justify-center transition-all shrink-0"
                                style="border: 2px solid {{ $sp->hex_color }};"
                                :style="completed ? 'background: {{ $sp->hex_color }}; border-color: {{ $sp->hex_color }}; box-shadow: 0 2px 6px {{ $sp->hex_color }}40;' : 'background: transparent;'">
                                <svg x-show="completed" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
                        <div x-show="showNote" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-4 pb-2.5 pl-20 relative">
                            <textarea x-model="note"
                                @blur="fetch('{{ url('admin/api/practices/logs') }}/{{ $sLog->id }}/note', { method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: JSON.stringify({note: note}) }).then(()=>{ noteSaved=true; setTimeout(()=>noteSaved=false, 1500); })"
                                placeholder="How did this go? Any thoughts, friction, or wins worth remembering..."
                                rows="2"
                                class="w-full px-2.5 py-2 rounded-lg border border-slate-200 text-[11px] text-slate-600 bg-slate-50 resize-y outline-none font-[inherit] transition-colors focus:border-slate-400"></textarea>
                            <span x-show="noteSaved" x-transition.opacity class="absolute right-5 bottom-3.5 text-[10px] text-green-600 font-semibold">Saved</span>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         SECTION 4c — Upskilling Widget
    ═══════════════════════════════════════════════ --}}
    <div x-data="upskillWidget()" class="mb-7">
        <div class="flex items-center justify-between mb-3 px-1">
            <span class="text-[13px] font-bold text-slate-800 tracking-tight">🧠 Today's Upskill</span>
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
                        class="w-full py-3 rounded-xl border-0 cursor-pointer text-sm font-semibold text-white bg-purple-500 transition-all shadow-[0_2px_8px_rgba(168,111,223,0.3)] hover:bg-purple-600 disabled:opacity-60 disabled:cursor-default">
                        ▶ Start Session
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
                        class="w-full py-2.5 rounded-lg border border-slate-200 bg-white cursor-pointer text-[13px] font-semibold text-red-500 transition-all hover:bg-red-50 hover:border-red-200">
                        ⏹ End Session
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
                          x-text="(groups['{{ $block->id }}'] || []).filter(t=>!t.is_completed).length"></span>
                </div>
                <div class="task-group bg-white border border-slate-200 rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow min-h-[4px] overflow-hidden" data-block-id="{{ $block->id }}">
                    <template x-for="task in sortedGroup('{{ $block->id }}')" :key="task.id">
                        <div class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100 last:border-b-0 transition-all hover:bg-slate-50/50" :data-task-id="task.id"
                             :style="(task.is_rolled_over ? 'border-left: 3px solid #f59e0b;' : '') + (task.is_completed ? 'opacity: 0.4;' : '')">
                            <div class="drag-handle cursor-grab text-slate-300 shrink-0 touch-none p-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
                            </div>
                            <span class="w-2 h-2 rounded-full shrink-0" :style="'background:' + priorityColor(task.priority)"></span>
                            <span class="flex-1 min-w-0 text-sm font-medium text-slate-800 truncate" :class="task.is_completed && 'line-through !text-slate-400'" x-text="task.title"></span>
                            <template x-if="task.pillar">
                                <span :class="pillarClasses(task.pillar)" class="px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap" x-text="task.pillar"></span>
                            </template>
                            <template x-if="task.is_rolled_over">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-800 whitespace-nowrap">↩ <span x-text="task.rollover_count"></span></span>
                            </template>
                            <template x-if="task.estimated_minutes">
                                <span class="text-[11px] text-slate-400 whitespace-nowrap font-medium" x-text="task.estimated_minutes + 'm'"></span>
                            </template>
                            <div class="flex gap-1.5 shrink-0">
                                <button class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-sm transition-all hover:bg-slate-50 hover:border-slate-300 active:scale-[0.92]" @click="completeTask(task)" :disabled="task.is_completed" :class="task.is_completed && 'opacity-25 !cursor-default'" title="Complete">✓</button>
                                <button class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-sm transition-all hover:bg-slate-50 hover:border-slate-300 active:scale-[0.92]" @click="deferTask(task)" title="Defer to tomorrow">→</button>
                                <button class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-sm text-red-500 transition-all hover:bg-slate-50 hover:border-slate-300 active:scale-[0.92]" @click="deleteTask(task)" title="Delete">✕</button>
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
                      x-text="(groups['anytime'] || []).filter(t=>!t.is_completed).length"></span>
            </div>
            <div class="task-group bg-white border border-slate-200 rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow min-h-[4px] overflow-hidden" data-block-id="anytime">
                <template x-for="task in sortedGroup('anytime')" :key="task.id">
                    <div class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100 last:border-b-0 transition-all hover:bg-slate-50/50" :data-task-id="task.id"
                         :style="(task.is_rolled_over ? 'border-left: 3px solid #f59e0b;' : '') + (task.is_completed ? 'opacity: 0.4;' : '')">
                        <div class="drag-handle cursor-grab text-slate-300 shrink-0 touch-none p-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
                        </div>
                        <span class="w-2 h-2 rounded-full shrink-0" :style="'background:' + priorityColor(task.priority)"></span>
                        <span class="flex-1 min-w-0 text-sm font-medium text-slate-800 truncate" :class="task.is_completed && 'line-through !text-slate-400'" x-text="task.title"></span>
                        <template x-if="task.pillar">
                            <span :class="pillarClasses(task.pillar)" class="px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap" x-text="task.pillar"></span>
                        </template>
                        <template x-if="task.is_rolled_over">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-800 whitespace-nowrap">↩ <span x-text="task.rollover_count"></span></span>
                        </template>
                        <template x-if="task.estimated_minutes">
                            <span class="text-[11px] text-slate-400 whitespace-nowrap font-medium" x-text="task.estimated_minutes + 'm'"></span>
                        </template>
                        <div class="flex gap-1.5 shrink-0">
                            <button class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-sm transition-all hover:bg-slate-50 hover:border-slate-300 active:scale-[0.92]" @click="completeTask(task)" :disabled="task.is_completed" :class="task.is_completed && 'opacity-25 !cursor-default'" title="Complete">✓</button>
                            <button class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-sm transition-all hover:bg-slate-50 hover:border-slate-300 active:scale-[0.92]" @click="deferTask(task)" title="Defer to tomorrow">→</button>
                            <button class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-sm text-red-500 transition-all hover:bg-slate-50 hover:border-slate-300 active:scale-[0.92]" @click="deleteTask(task)" title="Delete">✕</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         SECTION 6 — Quick Add Drawer
    ═══════════════════════════════════════════════ --}}
    <div x-data="quickAdd()" @keydown.escape.window="open = false">
        {{-- FAB --}}
        <button @click="open = true"
            class="fixed bottom-7 sm:bottom-7 right-7 sm:right-7 w-[52px] h-[52px] sm:w-[58px] sm:h-[58px] rounded-[14px] sm:rounded-[18px] border-0 cursor-pointer text-[26px] sm:text-[30px] text-white flex items-center justify-center z-50 transition-transform hover:scale-105 active:scale-95"
            style="background: var(--day-color); box-shadow: 0 6px 24px var(--day-shadow);">+</button>

        {{-- Overlay --}}
        <div x-show="open" x-transition.opacity @click="open = false"
             class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm z-[60]"></div>

        {{-- Sheet --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl px-6 pt-7 pb-9 z-[70] max-w-[560px] mx-auto shadow-[0_-8px_40px_rgba(0,0,0,0.12)]">

            <div class="w-9 h-1 rounded-sm bg-slate-200 mx-auto mb-6"></div>

            {{-- Title input --}}
            <input type="text" x-model="title" x-ref="titleInput"
                   @keydown.enter="submit()"
                   placeholder="Task title..."
                   class="w-full text-lg font-semibold text-slate-800 py-3.5 border-0 border-b-2 border-slate-100 outline-none bg-transparent mb-5 tracking-tight placeholder:text-slate-300 placeholder:font-normal"
                   x-init="$watch('open', v => { if(v) setTimeout(()=>$refs.titleInput.focus(), 100) })">

            {{-- AI Task Suggestions --}}
            @if(!empty($aiSuggestions))
            <div class="mb-5" x-show="!title.trim()">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">✨ Suggested for today</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($aiSuggestions as $sug)
                    <button type="button"
                        @click="title = '{{ addslashes($sug['title'] ?? '') }}'; priority = '{{ $sug['priority'] ?? 'should' }}'; pillar = '{{ $sug['pillar'] ?? '' }}';"
                        class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-600 font-medium hover:bg-slate-50 cursor-pointer transition-all flex items-center gap-1.5"
                        title="{{ $sug['reason'] ?? '' }}">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ ($sug['priority'] ?? 'should') === 'must' ? '#ef4444' : (($sug['priority'] ?? 'should') === 'should' ? '#f59e0b' : '#22c55e') }};"></span>
                        {{ $sug['title'] ?? '' }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

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
            <span class="text-xl leading-none shrink-0">⚠️</span>
            <p class="flex-1 text-sm font-semibold text-slate-800 leading-tight">Overloaded Day Detected</p>
            <button @click="show = false" class="text-slate-300 hover:text-slate-500 text-base leading-none cursor-pointer p-0.5 -mt-0.5 transition-colors">✕</button>
        </div>
        <div class="px-4 pb-2">
            <p class="text-xs text-slate-500 leading-relaxed">{{ $overloadWarning['message'] ?? 'You have too many tasks today.' }}</p>
        </div>
        @if(!empty($overloadWarning['defer_suggestions']))
        <div class="px-4 pb-3 space-y-1.5">
            @foreach($overloadWarning['defer_suggestions'] as $deferTitle)
                @php $deferTask = $plan->tasks()->where('title', $deferTitle)->where('is_completed', false)->first(); @endphp
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
    const CSRF = '{{ csrf_token() }}';
    const PLAN_ID = {{ $plan->id }};
    const API = {
        store: '{{ route("admin.api.tasks.store") }}',
        complete: id => '{{ url("admin/api/tasks") }}/' + id + '/complete',
        defer: id => '{{ url("admin/api/tasks") }}/' + id + '/defer',
        destroy: id => '{{ url("admin/api/tasks") }}/' + id,
        reorder: '{{ route("admin.api.tasks.reorder") }}',
    };

    const PILLAR_MAP = @js($pillarColors);

    function taskList() {
        return {
            groups: @js($groupedTasks),
            sortables: [],

            init() {
                this.$nextTick(() => this.initSortable());
            },

            initSortable() {
                document.querySelectorAll('.task-group').forEach(el => {
                    const blockId = el.dataset.blockId;
                    Sortable.create(el, {
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'opacity-30',
                        onEnd: (evt) => {
                            const items = [];
                            el.querySelectorAll('[data-task-id]').forEach((row, i) => {
                                items.push({ id: parseInt(row.dataset.taskId), sort_order: i });
                            });
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
                return [...tasks].sort((a,b) => {
                    if (a.is_completed !== b.is_completed) return a.is_completed ? 1 : -1;
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

            async completeTask(task) {
                if (task.is_completed) return;
                task.is_completed = true;
                task.completed_at = new Date().toISOString();
                this.updateStats(1, 0);
                await fetch(API.complete(task.id), {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                });
            },

            async deferTask(task) {
                const blockKey = task.time_block_id ? String(task.time_block_id) : 'anytime';
                this.groups[blockKey] = (this.groups[blockKey] || []).filter(t => t.id !== task.id);
                this.updateStats(task.is_completed ? -1 : 0, -1);
                await fetch(API.defer(task.id), {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                });
            },

            async deleteTask(task) {
                const blockKey = task.time_block_id ? String(task.time_block_id) : 'anytime';
                this.groups[blockKey] = (this.groups[blockKey] || []).filter(t => t.id !== task.id);
                this.updateStats(task.is_completed ? -1 : 0, -1);
                await fetch(API.destroy(task.id), {
                    method: 'DELETE',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
                });
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
            blockId: '',
            pillar: '',
            minutes: '',

            priorityBg(p) {
                return p === 'must' ? '#ef4444' : p === 'bonus' ? '#22c55e' : '#f59e0b';
            },

            async submit() {
                if (!this.title.trim()) return;
                const body = {
                    daily_plan_id: PLAN_ID,
                    title: this.title.trim(),
                    priority: this.priority,
                    time_block_id: this.blockId || null,
                    pillar: this.pillar || null,
                    estimated_minutes: this.minutes ? parseInt(this.minutes) : null,
                };
                const res = await fetch(API.store, {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF,'Accept':'application/json'},
                    body: JSON.stringify(body)
                });
                if (res.ok) {
                    const task = await res.json();
                    const tl = document.querySelector('[x-data="taskList()"]');
                    if (tl) {
                        const data = Alpine.$data(tl);
                        if (data && data.addTask) data.addTask(task);
                    }
                    this.title = '';
                    this.priority = 'should';
                    this.blockId = '';
                    this.pillar = '';
                    this.minutes = '';
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
</script>
@endpush
@endsection
