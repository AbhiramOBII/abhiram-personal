@extends('admin.layouts.app')

@section('title', 'Practices')

@php
    $pillarList = ['revenue','operations','marketing','growth','content','creation','product','networking','community','media','brand','podcast','health','recovery','learning'];
    $dayLabels = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
@endphp

@push('head')
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section('content')
<div class="max-w-[900px] mx-auto pb-16" x-data="practiceManager()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-[22px] font-bold text-slate-800 tracking-tight">Practices</h1>
            <p class="mt-0.5 text-[13px] text-slate-400">Build streaks, stack habits, use two-minute fallbacks on hard days.</p>
        </div>
        <button @click="openModal()" class="px-5 py-2.5 rounded-xl border-0 bg-slate-800 text-white text-[13px] font-semibold cursor-pointer hover:bg-slate-700 transition-colors shadow-sm">
            + Add Practice
        </button>
    </div>

    {{-- Type tabs --}}
    <div class="flex mb-6 bg-slate-100 rounded-xl p-1 gap-1">
        <button @click="activeTab = 'behavioral'"
                class="flex-1 py-2.5 px-4 rounded-lg text-[13px] font-semibold border-0 cursor-pointer transition-all duration-200"
                :class="activeTab === 'behavioral' ? 'bg-white text-slate-800 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700'">
            ✅ Behavioral ({{ $behavioral->count() }})
        </button>
        <button @click="activeTab = 'reflective'"
                class="flex-1 py-2.5 px-4 rounded-lg text-[13px] font-semibold border-0 cursor-pointer transition-all duration-200"
                :class="activeTab === 'reflective' ? 'bg-white text-slate-800 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700'">
            🧘 Reflective ({{ $reflective->count() }})
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200">
            <p class="text-sm text-green-700 m-0">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Practice cards --}}
    @if($practices->count())
    <div class="space-y-4">
        @foreach($practices as $practice)
        <template x-if="activeTab === '{{ $practice->type }}'">
            @php
                $sd = $streakData[$practice->id] ?? ['current_streak'=>0,'longest_streak'=>0,'completion_rate_30'=>0,'total_completions'=>0];
                $hm = $heatmapData[$practice->id] ?? [];
                $todayLog = $todayLogs[$practice->id] ?? null;
            @endphp
            <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-md transition-shadow" style="border-left: 4px solid {{ $practice->hex_color }};">

                {{-- Top row --}}
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $practice->hex_color }}12;">
                            <x-practice-icon :practice="$practice" size="24" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-[15px] font-semibold text-slate-800 m-0 truncate">{{ $practice->name }}</h3>
                            @if($practice->identity_statement)
                                <p class="text-[11px] text-slate-400 italic mt-0.5 truncate">{{ $practice->identity_statement }}</p>
                            @endif
                            @if($practice->description && $practice->isReflective())
                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $practice->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-1.5 shrink-0">
                        <button @click="openModal({{ $practice->id }})" class="w-8 h-8 rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-slate-400 hover:text-slate-600 hover:border-slate-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form method="POST" action="{{ route('admin.practices.destroy', $practice) }}" class="inline" onsubmit="return confirm('Deactivate this practice?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-red-400 hover:text-red-500 hover:border-red-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Cue → Reward --}}
                @if($practice->cue || $practice->reward)
                <div class="mb-3 px-3 py-2 rounded-lg bg-slate-50 text-xs text-slate-500">
                    @if($practice->cue)<span class="font-medium text-slate-600">Cue:</span> {{ $practice->cue }}@endif
                    @if($practice->cue && $practice->reward) <span class="mx-1 text-slate-300">&rarr;</span> @endif
                    @if($practice->reward)<span class="font-medium text-slate-600">Reward:</span> {{ $practice->reward }}@endif
                </div>
                @endif

                {{-- Prompt template (reflective) --}}
                @if($practice->isReflective() && $practice->prompt_template)
                <div class="mb-3 px-3 py-2 rounded-lg bg-indigo-50/60 border border-indigo-100 text-[11px] text-indigo-700">
                    <span class="font-semibold text-indigo-500">Prompt:</span> {{ Str::limit($practice->prompt_template, 120) }}
                </div>
                @endif

                {{-- Quantified badge --}}
                @if($practice->isQuantified())
                <div class="mb-3 px-3 py-2 rounded-lg bg-blue-50 border border-blue-100 text-[11px] text-blue-700">
                    <span class="font-semibold">Target:</span> {{ $practice->target_value }} {{ $practice->unit }}
                </div>
                @endif

                {{-- Two-minute version --}}
                @if($practice->two_minute_version)
                <div class="mb-3 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-100 text-[11px] text-amber-800">
                    <span class="font-semibold">2-min:</span> {{ $practice->two_minute_version }}
                </div>
                @endif

                {{-- Stats row --}}
                <div class="flex gap-5 mb-3 flex-wrap">
                    <div class="text-center">
                        <div class="flex items-baseline gap-0.5">
                            <span class="text-lg font-bold text-slate-800">{{ $sd['current_streak'] }}</span>
                            @if($sd['current_streak'] >= 3)<span class="text-sm">🔥</span>@endif
                        </div>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold mt-0.5">Streak</p>
                    </div>
                    <div class="text-center">
                        <span class="text-lg font-bold text-slate-800">{{ $sd['longest_streak'] }}</span>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold mt-0.5">Best</p>
                    </div>
                    <div class="text-center">
                        <span class="text-lg font-bold text-slate-800">{{ $sd['total_completions'] }}</span>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold mt-0.5">Total</p>
                    </div>
                    <div class="flex-1 min-w-[100px]">
                        <div class="flex justify-between mb-1">
                            <span class="text-[9px] text-slate-400 font-semibold uppercase tracking-wider">30-day</span>
                            <span class="text-[10px] text-slate-600 font-semibold">{{ $sd['completion_rate_30'] }}%</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" style="background: {{ $practice->hex_color }}; width: {{ $sd['completion_rate_30'] }}%;"></div>
                        </div>
                    </div>
                </div>

                {{-- 30-day heatmap --}}
                <div class="flex gap-[3px] flex-wrap mb-3">
                    @for($i = 29; $i >= 0; $i--)
                        @php $d = now()->subDays($i)->toDateString(); $filled = in_array($d, $hm); @endphp
                        <div title="{{ $d }}" class="w-[11px] h-[11px] rounded-sm transition-colors {{ $filled ? '' : 'bg-slate-100' }}" @if($filled) style="background: {{ $practice->hex_color }};" @endif></div>
                    @endfor
                </div>

                {{-- Badges --}}
                <div class="flex gap-1.5 flex-wrap items-center">
                    @if($practice->frequency_type === 'daily')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-500">Daily</span>
                    @else
                        @foreach($practice->frequency_days ?? [] as $dn)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-500">{{ $dayLabels[$dn] ?? $dn }}</span>
                        @endforeach
                    @endif
                    @if($practice->isReflective())
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 text-indigo-600">{{ ucfirst(str_replace('_', ' ', $practice->input_type)) }}</span>
                    @endif
                    @if($practice->stackAfter)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-sky-50 text-sky-700">↳ after {{ $practice->stackAfter->name }}</span>
                    @endif
                    @if(count($notesData[$practice->id] ?? []))
                        <button @click="openNotes({{ $practice->id }}, '{{ addslashes($practice->icon_emoji) }} {{ addslashes($practice->name) }}')" type="button"
                            class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-sky-50 text-sky-600 border border-sky-100 cursor-pointer hover:bg-sky-100 transition-colors">
                            📝 Notes ({{ count($notesData[$practice->id]) }})
                        </button>
                    @endif
                </div>
            </div>
        </template>
        @endforeach
    </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl py-16 px-6 text-center">
            <p class="text-[15px] text-slate-400 m-0">No practices yet — click "+ Add Practice" to create your first one.</p>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         View Notes Modal
    ═══════════════════════════════════════════════ --}}
    <div x-show="showNotes" x-cloak x-transition.opacity @click.self="showNotes = false" @keydown.escape.window="showNotes = false"
         class="fixed inset-0 bg-black/30 z-[60] flex items-center justify-center p-4">
        <div x-show="showNotes" x-transition class="bg-white rounded-2xl p-6 w-full max-w-[560px] max-h-[80vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-slate-800" x-text="notesTitle"></h2>
                <button @click="showNotes = false" class="w-8 h-8 rounded-lg border border-slate-200 bg-white cursor-pointer flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <template x-if="notesList.length === 0">
                <p class="text-[13px] text-slate-400 text-center py-8">No notes yet for this practice.</p>
            </template>
            <template x-for="(entry, idx) in notesList" :key="idx">
                <div class="py-3.5 border-b border-slate-100 last:border-b-0">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-xs font-semibold text-slate-700" x-text="new Date(entry.logged_date).toLocaleDateString('en-IN', {day:'numeric',month:'short',year:'numeric'})"></span>
                        <template x-if="entry.used_two_minute_version">
                            <span class="px-1.5 py-px rounded text-[9px] font-bold bg-amber-100 text-amber-800">2-min</span>
                        </template>
                    </div>
                    <p class="text-[13px] text-slate-600 m-0 leading-relaxed whitespace-pre-wrap" x-text="entry.note"></p>
                </div>
            </template>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         Add / Edit Modal
    ═══════════════════════════════════════════════ --}}
    <div x-show="showModal" x-cloak x-transition.opacity @click.self="showModal = false" @keydown.escape.window="showModal = false"
         class="fixed inset-0 bg-black/30 z-[60] flex items-center justify-center p-4">
        <div x-show="showModal" x-transition class="bg-white rounded-2xl p-6 w-full max-w-[560px] max-h-[90vh] overflow-y-auto shadow-2xl">
            <h2 class="text-lg font-bold text-slate-800 mb-5" x-text="editId ? 'Edit Practice' : 'Add Practice'"></h2>

            <form :action="editId ? '{{ url('admin/practices') }}/' + editId : '{{ route('admin.practices.store') }}'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="editId"><input type="hidden" name="_method" value="PATCH"></template>

                {{-- Type toggle --}}
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Type *</label>
                    <div class="flex gap-2">
                        <button type="button" @click="form.type = 'behavioral'"
                                class="px-4 py-2 rounded-lg text-xs font-medium border cursor-pointer transition-all"
                                :class="form.type === 'behavioral' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-500 border-slate-200 hover:border-slate-300'">
                            ✅ Behavioral
                        </button>
                        <button type="button" @click="form.type = 'reflective'"
                                class="px-4 py-2 rounded-lg text-xs font-medium border cursor-pointer transition-all"
                                :class="form.type === 'reflective' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-500 border-slate-200 hover:border-slate-300'">
                            🧘 Reflective
                        </button>
                    </div>
                    <input type="hidden" name="type" :value="form.type">
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Name *</label>
                    <input type="text" name="name" x-model="form.name" required class="admin-input w-full">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Description</label>
                    <textarea name="description" x-model="form.description" rows="2" class="admin-input w-full resize-y"></textarea>
                </div>

                {{-- SVG Icon Upload + Fallback Emoji --}}
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Icon</label>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-slate-50 border border-slate-200 shrink-0">
                            <img x-show="iconPreview" :src="iconPreview" class="w-8 h-8 object-contain">
                            <span x-show="!iconPreview" class="text-2xl" x-text="form.icon_fallback_emoji || '✨'"></span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="inline-flex px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-500 cursor-pointer hover:bg-slate-50 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Upload SVG
                                <input type="file" name="icon" accept=".svg" class="hidden" @change="previewIcon($event)">
                            </label>
                            <span class="text-[10px] text-slate-400">Max 100KB</span>
                            <button x-show="iconPreview" @click="iconPreview = null" type="button" class="text-[10px] text-red-500 bg-transparent border-0 cursor-pointer p-0 text-left hover:text-red-600">Remove</button>
                        </div>
                        <div class="w-20 shrink-0">
                            <label class="block text-[10px] font-semibold text-slate-400 mb-0.5">or emoji</label>
                            <input type="text" name="icon_fallback_emoji" x-model="form.icon_fallback_emoji" class="admin-input w-full text-center text-lg !py-1.5">
                        </div>
                    </div>
                </div>

                {{-- Reflective-only fields --}}
                <template x-if="form.type === 'reflective'">
                    <div class="space-y-4 pt-1 border-t border-indigo-100">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 pt-2">Reflective Settings</p>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Prompt Template *</label>
                            <textarea name="prompt_template" x-model="form.prompt_template" rows="3" class="admin-input w-full resize-y" placeholder="Generate a {theme}-day vision prompt for a founder focused on {pillar}."></textarea>
                            <p class="text-[10px] text-slate-400 mt-1">Variables: <code class="text-indigo-500">{theme}</code> <code class="text-indigo-500">{day}</code> <code class="text-indigo-500">{pillar}</code></p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Input Type</label>
                            <div class="flex gap-2">
                                <button type="button" @click="form.input_type = 'text_short'"
                                        class="px-3 py-1.5 rounded-lg text-[11px] font-medium border cursor-pointer transition-all"
                                        :class="form.input_type === 'text_short' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-500 border-slate-200'">Short</button>
                                <button type="button" @click="form.input_type = 'text_long'"
                                        class="px-3 py-1.5 rounded-lg text-[11px] font-medium border cursor-pointer transition-all"
                                        :class="form.input_type === 'text_long' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-500 border-slate-200'">Long</button>
                                <button type="button" @click="form.input_type = 'list'"
                                        class="px-3 py-1.5 rounded-lg text-[11px] font-medium border cursor-pointer transition-all"
                                        :class="form.input_type === 'list' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-500 border-slate-200'">List</button>
                            </div>
                            <input type="hidden" name="input_type" :value="form.input_type">
                        </div>
                    </div>
                </template>

                {{-- Behavioral-only fields --}}
                <template x-if="form.type === 'behavioral'">
                    <div class="space-y-4 pt-1 border-t border-emerald-100">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-500 pt-2">Habit Loop</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Cue</label>
                                <input type="text" name="cue" x-model="form.cue" class="admin-input w-full" placeholder="After morning tea">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Reward</label>
                                <input type="text" name="reward" x-model="form.reward" class="admin-input w-full" placeholder="Feel sharp">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Identity Statement</label>
                            <input type="text" name="identity_statement" x-model="form.identity_statement" class="admin-input w-full" placeholder="I am someone who...">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Two-Minute Version</label>
                            <input type="text" name="two_minute_version" x-model="form.two_minute_version" class="admin-input w-full" placeholder="Stripped-down for hard days">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer mb-2">
                                <input type="checkbox" x-model="form.is_quantified" class="accent-slate-800 w-4 h-4">
                                <span class="text-[13px] text-slate-700 font-medium">Quantified (track a number)</span>
                            </label>
                            <div x-show="form.is_quantified" x-transition class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Unit</label>
                                    <input type="text" name="unit" x-model="form.unit" class="admin-input w-full" placeholder="glasses, steps">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Target</label>
                                    <input type="number" name="target_value" x-model="form.target_value" class="admin-input w-full" min="1" placeholder="8">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Pillar + Color --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Pillar</label>
                        <select name="pillar" x-model="form.pillar" class="admin-input w-full">
                            <option value="">None</option>
                            @foreach($pillarList as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Color</label>
                        <input type="color" name="hex_color" x-model="form.hex_color" class="w-full h-[42px] rounded-xl border border-slate-200 p-1 cursor-pointer">
                    </div>
                </div>

                {{-- Frequency --}}
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Frequency</label>
                    <div class="flex gap-2 mb-2">
                        <button type="button" @click="form.frequency_type = 'daily'"
                                class="px-4 py-2 rounded-lg text-xs font-medium border cursor-pointer transition-all"
                                :class="form.frequency_type === 'daily' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-500 border-slate-200'">Daily</button>
                        <button type="button" @click="form.frequency_type = 'specific_days'"
                                class="px-4 py-2 rounded-lg text-xs font-medium border cursor-pointer transition-all"
                                :class="form.frequency_type === 'specific_days' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-500 border-slate-200'">Specific Days</button>
                    </div>
                    <input type="hidden" name="frequency_type" :value="form.frequency_type">
                    <div x-show="form.frequency_type === 'specific_days'" x-transition class="flex gap-1.5 flex-wrap">
                        @foreach($dayLabels as $dn => $dl)
                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="checkbox" name="frequency_days[]" value="{{ $dn }}" :checked="(form.frequency_days || []).includes({{ $dn }})" class="accent-slate-800">
                                {{ $dl }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Stack after + trigger --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Stack After</label>
                        <select name="stack_after_practice_id" x-model="form.stack_after_practice_id" class="admin-input w-full">
                            <option value="">None</option>
                            @foreach($allPractices as $ap)
                                <option value="{{ $ap->id }}">{{ $ap->icon_emoji }} {{ $ap->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Stack Trigger</label>
                        <input type="text" name="stack_trigger" x-model="form.stack_trigger" class="admin-input w-full" placeholder="Right after...">
                    </div>
                </div>

                {{-- Two-minute enabled --}}
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_two_minute_enabled" value="0">
                        <input type="checkbox" name="is_two_minute_enabled" value="1" :checked="form.is_two_minute_enabled" class="accent-slate-800 w-4 h-4">
                        <span class="text-[13px] text-slate-700 font-medium">Enable two-minute fallback</span>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 justify-end pt-2 border-t border-slate-100">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-500 text-[13px] font-medium cursor-pointer hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl border-0 bg-slate-800 text-white text-[13px] font-semibold cursor-pointer hover:bg-slate-700 transition-colors shadow-sm" x-text="editId ? 'Save Changes' : 'Create Practice'"></button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    const practicesData = @js($practices->keyBy('id')->toArray());
    const allNotesData = @js($notesData);

    function practiceManager() {
        return {
            activeTab: 'behavioral',
            showModal: false,
            showNotes: false,
            notesTitle: '',
            notesList: [],
            editId: null,
            iconPreview: null,
            form: {
                name: '', type: 'behavioral', description: '', cue: '', reward: '', identity_statement: '',
                two_minute_version: '', pillar: '', hex_color: '#4f98a3', icon_emoji: '✅',
                icon_fallback_emoji: '✅', prompt_template: '', input_type: 'text_long',
                unit: '', target_value: '', is_quantified: false,
                frequency_type: 'daily', frequency_days: [],
                stack_after_practice_id: '', stack_trigger: '', is_two_minute_enabled: true,
            },

            previewIcon(e) {
                const file = e.target.files[0];
                if (!file) return;
                if (file.size > 102400) { alert('SVG must be under 100KB'); return; }
                const reader = new FileReader();
                reader.onload = (ev) => { this.iconPreview = ev.target.result; };
                reader.readAsDataURL(file);
            },

            openNotes(practiceId, title) {
                this.notesTitle = title;
                this.notesList = allNotesData[practiceId] || [];
                this.showNotes = true;
            },

            openModal(id = null) {
                this.editId = id;
                this.iconPreview = null;
                if (id && practicesData[id]) {
                    const p = practicesData[id];
                    this.iconPreview = p.icon_url || null;
                    this.form = {
                        name: p.name || '',
                        type: p.type || 'behavioral',
                        description: p.description || '',
                        cue: p.cue || '',
                        reward: p.reward || '',
                        identity_statement: p.identity_statement || '',
                        two_minute_version: p.two_minute_version || '',
                        pillar: p.pillar || '',
                        hex_color: p.hex_color || '#4f98a3',
                        icon_emoji: p.icon_emoji || '✅',
                        icon_fallback_emoji: p.icon_fallback_emoji || p.icon_emoji || '✅',
                        prompt_template: p.prompt_template || '',
                        input_type: p.input_type || 'text_long',
                        unit: p.unit || '',
                        target_value: p.target_value || '',
                        is_quantified: !!(p.target_value),
                        frequency_type: p.frequency_type || 'daily',
                        frequency_days: p.frequency_days || [],
                        stack_after_practice_id: p.stack_after_practice_id || '',
                        stack_trigger: p.stack_trigger || '',
                        is_two_minute_enabled: p.is_two_minute_enabled ?? true,
                    };
                } else {
                    this.form = {
                        name: '', type: this.activeTab, description: '', cue: '', reward: '', identity_statement: '',
                        two_minute_version: '', pillar: '', hex_color: '#4f98a3', icon_emoji: '✅',
                        icon_fallback_emoji: '✅', prompt_template: '', input_type: 'text_long',
                        unit: '', target_value: '', is_quantified: false,
                        frequency_type: 'daily', frequency_days: [],
                        stack_after_practice_id: '', stack_trigger: '', is_two_minute_enabled: true,
                    };
                }
                this.showModal = true;
            }
        };
    }
</script>
@endpush
@endsection
