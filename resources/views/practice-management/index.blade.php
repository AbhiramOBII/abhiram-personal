@extends('admin.layouts.app')

@section('title', 'Practices')

@php
    $pillarList = ['revenue','operations','marketing','growth','content','creation','product','networking','community','media','brand','podcast','health','recovery','learning'];
    $dayLabels = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
@endphp

@section('content')
<div style="max-width: 900px; margin: 0 auto;" x-data="practiceManager()">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Practices</h1>
            <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">Build streaks, stack habits, use two-minute fallbacks on hard days.</p>
        </div>
        <button @click="openModal()" style="padding: 10px 18px; border-radius: 8px; border: none; background: #1e293b; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;">+ Add Practice</button>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Practice cards grid --}}
    @if($practices->count())
    <div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 16px;">
        @media (min-width: 768px) {} {{-- handled via inline style below --}}
        @foreach($practices as $practice)
            @php
                $sd = $streakData[$practice->id] ?? ['current_streak'=>0,'longest_streak'=>0,'completion_rate_30'=>0,'total_completions'=>0];
                $hm = $heatmapData[$practice->id] ?? [];
                $todayLog = $todayLogs[$practice->id] ?? null;
            @endphp
            <div class="admin-card" style="padding: 20px; border-radius: 14px; border-left: 4px solid {{ $practice->hex_color }};">
                {{-- Top row --}}
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                        <span style="font-size: 28px; line-height: 1;">{{ $practice->icon_emoji }}</span>
                        <div style="min-width: 0;">
                            <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0;">{{ $practice->name }}</h3>
                            @if($practice->identity_statement)
                                <p style="font-size: 12px; color: #94a3b8; font-style: italic; margin: 2px 0 0;">{{ $practice->identity_statement }}</p>
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; gap: 4px; flex-shrink: 0;">
                        <button @click="openModal({{ $practice->id }})" style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #64748b;">✎</button>
                        <form method="POST" action="{{ route('admin.practices.destroy', $practice) }}" style="display:inline;" onsubmit="return confirm('Deactivate this practice?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #ef4444;">✕</button>
                        </form>
                    </div>
                </div>

                {{-- Cue → Reward --}}
                @if($practice->cue || $practice->reward)
                <div style="margin-bottom: 10px; padding: 8px 10px; border-radius: 8px; background: #f8f9fb; font-size: 12px; color: #64748b;">
                    @if($practice->cue)<span style="font-weight: 500;">Cue:</span> {{ $practice->cue }}@endif
                    @if($practice->cue && $practice->reward) &nbsp;→&nbsp; @endif
                    @if($practice->reward)<span style="font-weight: 500;">Reward:</span> {{ $practice->reward }}@endif
                </div>
                @endif

                {{-- Two-minute version --}}
                @if($practice->two_minute_version)
                <div style="margin-bottom: 10px; padding: 6px 10px; border-radius: 8px; background: #fffbeb; border: 1px solid #fef3c7; font-size: 11px; color: #92400e;">
                    <span style="font-weight: 600;">2-min version:</span> {{ $practice->two_minute_version }}
                </div>
                @endif

                {{-- Stats row --}}
                <div style="display: flex; gap: 16px; margin-bottom: 12px; flex-wrap: wrap;">
                    <div>
                        <span style="font-size: 20px; font-weight: 700; color: #1e293b;">{{ $sd['current_streak'] }}</span>
                        @if($sd['current_streak'] >= 3)<span style="font-size: 14px;">🔥</span>@endif
                        <p style="font-size: 10px; color: #94a3b8; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Current Streak</p>
                    </div>
                    <div>
                        <span style="font-size: 20px; font-weight: 700; color: #1e293b;">{{ $sd['longest_streak'] }}</span>
                        <p style="font-size: 10px; color: #94a3b8; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Longest</p>
                    </div>
                    <div>
                        <span style="font-size: 20px; font-weight: 700; color: #1e293b;">{{ $sd['total_completions'] }}</span>
                        <p style="font-size: 10px; color: #94a3b8; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Total</p>
                    </div>
                </div>

                {{-- 30-day completion bar --}}
                <div style="margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="font-size: 10px; color: #94a3b8; font-weight: 600;">30-day rate</span>
                        <span style="font-size: 10px; color: #64748b; font-weight: 600;">{{ $sd['completion_rate_30'] }}%</span>
                    </div>
                    <div style="width: 100%; height: 4px; border-radius: 2px; background: #f1f5f9; overflow: hidden;">
                        <div style="height: 100%; border-radius: 2px; background: {{ $practice->hex_color }}; width: {{ $sd['completion_rate_30'] }}%; transition: width 0.5s;"></div>
                    </div>
                </div>

                {{-- 30-day heatmap --}}
                <div style="display: flex; gap: 2px; flex-wrap: wrap; margin-bottom: 10px;">
                    @for($i = 29; $i >= 0; $i--)
                        @php $d = now()->subDays($i)->toDateString(); $filled = in_array($d, $hm); @endphp
                        <div title="{{ $d }}" style="width: 12px; height: 12px; border-radius: 2px; {{ $filled ? 'background: ' . $practice->hex_color . ';' : 'background: #f1f5f9;' }}"></div>
                    @endfor
                </div>

                {{-- Frequency badge --}}
                <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                    @if($practice->frequency_type === 'daily')
                        <span style="padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: #f1f5f9; color: #64748b;">Daily</span>
                    @else
                        @foreach($practice->frequency_days ?? [] as $dn)
                            <span style="padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: #f1f5f9; color: #64748b;">{{ $dayLabels[$dn] ?? $dn }}</span>
                        @endforeach
                    @endif
                    @if($practice->stackAfter)
                        <span style="padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: #e0f2fe; color: #0369a1;">↳ after {{ $practice->stackAfter->name }}</span>
                    @endif
                    @if(count($notesData[$practice->id] ?? []))
                        <button @click="openNotes({{ $practice->id }}, '{{ addslashes($practice->icon_emoji) }} {{ addslashes($practice->name) }}')" type="button" style="padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#e0f2fe'" onmouseout="this.style.background='#f0f9ff'">📝 View Notes ({{ count($notesData[$practice->id]) }})</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="admin-card" style="padding: 48px 24px; text-align: center;">
            <p style="font-size: 15px; color: #94a3b8; margin: 0;">No practices yet — click "Add Practice" to create your first one.</p>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         View Notes Modal
    ═══════════════════════════════════════════════ --}}
    <div x-show="showNotes" x-transition.opacity @click.self="showNotes = false" @keydown.escape.window="showNotes = false"
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 60; display: flex; align-items: center; justify-content: center; padding: 16px;">
        <div x-show="showNotes" x-transition style="background: #fff; border-radius: 16px; padding: 24px; width: 100%; max-width: 560px; max-height: 80vh; overflow-y: auto; box-shadow: 0 8px 40px rgba(0,0,0,0.12);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;" x-text="notesTitle"></h2>
                <button @click="showNotes = false" style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #64748b;">✕</button>
            </div>
            <template x-if="notesList.length === 0">
                <p style="font-size: 13px; color: #94a3b8; text-align: center; padding: 24px 0;">No notes yet for this practice.</p>
            </template>
            <template x-for="(entry, idx) in notesList" :key="idx">
                <div style="padding: 14px 0; border-bottom: 1px solid #f1f5f9;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <span style="font-size: 12px; font-weight: 600; color: #1e293b;" x-text="new Date(entry.logged_date).toLocaleDateString('en-IN', {day:'numeric',month:'short',year:'numeric'})"></span>
                        <template x-if="entry.used_two_minute_version">
                            <span style="padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 700; background: #fef3c7; color: #92400e;">2-min</span>
                        </template>
                    </div>
                    <p style="font-size: 13px; color: #475569; margin: 0; line-height: 1.5; white-space: pre-wrap;" x-text="entry.note"></p>
                </div>
            </template>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         Add / Edit Modal
    ═══════════════════════════════════════════════ --}}
    <div x-show="showModal" x-transition.opacity @click.self="showModal = false" @keydown.escape.window="showModal = false"
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 60; display: flex; align-items: center; justify-content: center; padding: 16px;">
        <div x-show="showModal" x-transition style="background: #fff; border-radius: 16px; padding: 24px; width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; box-shadow: 0 8px 40px rgba(0,0,0,0.12);">
            <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px;" x-text="editId ? 'Edit Practice' : 'Add Practice'"></h2>

            <form :action="editId ? '{{ url('admin/practices') }}/' + editId : '{{ route('admin.practices.store') }}'" method="POST">
                @csrf
                <template x-if="editId"><input type="hidden" name="_method" value="PATCH"></template>

                {{-- Name --}}
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Name *</label>
                    <input type="text" name="name" x-model="form.name" required class="admin-input">
                </div>

                {{-- Description --}}
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Description</label>
                    <textarea name="description" x-model="form.description" rows="2" class="admin-input" style="resize: vertical;"></textarea>
                </div>

                {{-- Cue + Reward --}}
                <div style="display: flex; gap: 12px; margin-bottom: 14px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Cue (trigger)</label>
                        <input type="text" name="cue" x-model="form.cue" class="admin-input" placeholder="e.g. After morning tea">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Reward</label>
                        <input type="text" name="reward" x-model="form.reward" class="admin-input" placeholder="e.g. Feel sharp and ready">
                    </div>
                </div>

                {{-- Identity statement --}}
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Identity Statement</label>
                    <input type="text" name="identity_statement" x-model="form.identity_statement" class="admin-input" placeholder="I am someone who...">
                </div>

                {{-- Two-minute version --}}
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Two-Minute Version</label>
                    <input type="text" name="two_minute_version" x-model="form.two_minute_version" class="admin-input" placeholder="Stripped-down version for hard days">
                </div>

                {{-- Pillar + Color + Emoji --}}
                <div style="display: flex; gap: 12px; margin-bottom: 14px;">
                    <div style="flex: 2;">
                        <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Pillar</label>
                        <select name="pillar" x-model="form.pillar" class="admin-input">
                            <option value="">None</option>
                            @foreach($pillarList as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Color</label>
                        <input type="color" name="hex_color" x-model="form.hex_color" style="width: 100%; height: 42px; border-radius: 10px; border: 1px solid #e5e7eb; padding: 4px; cursor: pointer;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Emoji</label>
                        <input type="text" name="icon_emoji" x-model="form.icon_emoji" class="admin-input" style="text-align: center; font-size: 20px;">
                    </div>
                </div>

                {{-- Frequency --}}
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Frequency</label>
                    <div style="display: flex; gap: 6px; margin-bottom: 8px;">
                        <button type="button" @click="form.frequency_type = 'daily'"
                                :style="form.frequency_type === 'daily' ? 'background:#1e293b;color:#fff;border-color:#1e293b;' : 'background:#fff;color:#64748b;border-color:#e5e7eb;'"
                                style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid; cursor: pointer;">Daily</button>
                        <button type="button" @click="form.frequency_type = 'specific_days'"
                                :style="form.frequency_type === 'specific_days' ? 'background:#1e293b;color:#fff;border-color:#1e293b;' : 'background:#fff;color:#64748b;border-color:#e5e7eb;'"
                                style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid; cursor: pointer;">Specific Days</button>
                    </div>
                    <input type="hidden" name="frequency_type" :value="form.frequency_type">
                    <div x-show="form.frequency_type === 'specific_days'" style="display: flex; gap: 6px; flex-wrap: wrap;">
                        @foreach($dayLabels as $dn => $dl)
                            <label style="display: flex; align-items: center; gap: 4px; padding: 6px 10px; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 12px; cursor: pointer;">
                                <input type="checkbox" name="frequency_days[]" value="{{ $dn }}" :checked="(form.frequency_days || []).includes({{ $dn }})" style="accent-color: #1e293b;">
                                {{ $dl }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Stack after + trigger --}}
                <div style="display: flex; gap: 12px; margin-bottom: 14px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Stack After</label>
                        <select name="stack_after_practice_id" x-model="form.stack_after_practice_id" class="admin-input">
                            <option value="">None</option>
                            @foreach($allPractices as $ap)
                                <option value="{{ $ap->id }}">{{ $ap->icon_emoji }} {{ $ap->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Stack Trigger</label>
                        <input type="text" name="stack_trigger" x-model="form.stack_trigger" class="admin-input" placeholder="e.g. Right after...">
                    </div>
                </div>

                {{-- Two-minute enabled --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="hidden" name="is_two_minute_enabled" value="0">
                        <input type="checkbox" name="is_two_minute_enabled" value="1" :checked="form.is_two_minute_enabled" style="accent-color: #1e293b; width: 16px; height: 16px;">
                        <span style="font-size: 13px; color: #1e293b; font-weight: 500;">Enable two-minute fallback</span>
                    </label>
                </div>

                {{-- Actions --}}
                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" @click="showModal = false" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; color: #64748b; font-size: 13px; font-weight: 500; cursor: pointer;">Cancel</button>
                    <button type="submit" style="padding: 10px 18px; border-radius: 8px; border: none; background: #1e293b; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;" x-text="editId ? 'Save Changes' : 'Create Practice'"></button>
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
            showModal: false,
            showNotes: false,
            notesTitle: '',
            notesList: [],
            editId: null,
            form: {
                name: '', description: '', cue: '', reward: '', identity_statement: '',
                two_minute_version: '', pillar: '', hex_color: '#4f98a3', icon_emoji: '✅',
                frequency_type: 'daily', frequency_days: [],
                stack_after_practice_id: '', stack_trigger: '', is_two_minute_enabled: true,
            },

            openNotes(practiceId, title) {
                this.notesTitle = title;
                this.notesList = allNotesData[practiceId] || [];
                this.showNotes = true;
            },

            openModal(id = null) {
                this.editId = id;
                if (id && practicesData[id]) {
                    const p = practicesData[id];
                    this.form = {
                        name: p.name || '',
                        description: p.description || '',
                        cue: p.cue || '',
                        reward: p.reward || '',
                        identity_statement: p.identity_statement || '',
                        two_minute_version: p.two_minute_version || '',
                        pillar: p.pillar || '',
                        hex_color: p.hex_color || '#4f98a3',
                        icon_emoji: p.icon_emoji || '✅',
                        frequency_type: p.frequency_type || 'daily',
                        frequency_days: p.frequency_days || [],
                        stack_after_practice_id: p.stack_after_practice_id || '',
                        stack_trigger: p.stack_trigger || '',
                        is_two_minute_enabled: p.is_two_minute_enabled ?? true,
                    };
                } else {
                    this.form = {
                        name: '', description: '', cue: '', reward: '', identity_statement: '',
                        two_minute_version: '', pillar: '', hex_color: '#4f98a3', icon_emoji: '✅',
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
