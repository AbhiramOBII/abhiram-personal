@extends('admin.layouts.app')

@section('title', 'Upskilling Zone')

@php
    $typeOptions = ['course', 'book', 'video', 'article', 'podcast', 'experiment'];
    $typeColors = [
        'course' => 'bg-purple-100 text-purple-700',
        'book' => 'bg-sky-100 text-sky-700',
        'video' => 'bg-red-100 text-red-700',
        'article' => 'bg-green-100 text-green-700',
        'podcast' => 'bg-orange-100 text-orange-700',
        'experiment' => 'bg-teal-100 text-teal-700',
    ];
    $typeHex = [
        'course' => '#a86fdf', 'book' => '#0284c7', 'video' => '#dc2626',
        'article' => '#16a34a', 'podcast' => '#ea580c', 'experiment' => '#0d9488',
    ];
@endphp

@section('content')
<div class="max-w-[960px] mx-auto" x-data="upskillingManager()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="font-['Space_Grotesk'] text-xl font-bold text-slate-800">🧠 Upskilling Zone</h1>
            <p class="mt-0.5 text-sm text-slate-400">Track learning, log sessions, build skills.</p>
        </div>
        <div class="flex gap-2">
            <button @click="openDomainModal()" class="px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 text-sm font-semibold hover:bg-slate-50 transition cursor-pointer">+ Domain</button>
            <button @click="openItemModal()" class="px-4 py-2.5 rounded-lg bg-purple-500 text-white text-sm font-semibold hover:bg-purple-600 transition cursor-pointer border-0">+ Learning Item</button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200">
            <p class="text-sm text-green-600">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-7">
        <div class="admin-card p-4 text-center">
            <p class="text-2xl font-bold text-purple-500">{{ $todayMinutes }}</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Min Today</p>
        </div>
        <div class="admin-card p-4 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ round($weekMinutes / 60, 1) }}</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Hours This Week</p>
        </div>
        <div class="admin-card p-4 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ round($monthMinutes / 60, 1) }}</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Hours This Month</p>
        </div>
        <div class="admin-card p-4 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ round($totalMinutes / 60, 1) }}</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Hours Total</p>
        </div>
    </div>

    {{-- Skill Domains (Accordion) --}}
    <div class="mb-7">
        <h2 class="text-base font-bold text-slate-800 mb-3.5">Skill Domains</h2>
        @if($domains->count())
        <div class="flex flex-col gap-2.5">
            @foreach($domains as $domain)
                @php $domainItems = $pendingItems->where('skill_domain_id', $domain->id); @endphp
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                    {{-- Domain Header --}}
                    <button @click="open = !open" class="w-full flex items-center gap-3 px-4 py-3.5 text-left cursor-pointer hover:bg-slate-50/60 transition-colors border-0 bg-transparent" :class="open && 'bg-slate-50/80'">
                        <span class="text-xl leading-none shrink-0">{{ $domain->icon_emoji }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-slate-800 truncate">{{ $domain->name }}</h3>
                                <span class="text-[10px] text-slate-400 shrink-0">Lv {{ $domain->current_level }}/{{ $domain->target_level }}</span>
                            </div>
                            <div class="w-full h-[3px] rounded-full bg-slate-100 mt-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="background: {{ $domain->hex_color }}; width: {{ $domain->progressPercentage() }}%;"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">{{ $domainItems->count() }} items</span>
                            <span class="text-[10px] text-slate-400 hidden sm:inline">{{ $domain->totalHoursLogged() }}h</span>
                            <span @click.stop="openDomainModal({{ $domain->id }})" class="w-6 h-6 rounded-md border border-slate-200 bg-white flex items-center justify-center text-[10px] text-slate-500 hover:bg-slate-50 cursor-pointer">✎</span>
                            <span class="text-[10px] text-slate-400 transition-transform duration-200" :class="open && 'rotate-180'">▼</span>
                        </div>
                    </button>

                    {{-- Items Panel --}}
                    <div x-show="open" x-collapse>
                        <div class="border-t border-slate-100">
                            @if($domainItems->count())
                                @foreach($domainItems as $item)
                                    <div class="flex items-center gap-2.5 px-4 py-2.5 pl-12 border-b border-slate-50 last:border-b-0 group" x-data="{ starting: false }">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background: {{ $typeHex[$item->type] ?? '#94a3b8' }};"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[13px] font-medium text-slate-800 truncate leading-snug">{{ $item->title }}</p>
                                            @if($item->notes)
                                                <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ Str::limit($item->notes, 70) }}</p>
                                            @endif
                                        </div>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold shrink-0 {{ $typeColors[$item->type] ?? 'bg-slate-100 text-slate-500' }}">{{ ucfirst($item->type) }}</span>
                                        @if($item->estimated_hours)
                                            <span class="text-[11px] text-slate-400 shrink-0 hidden sm:inline">{{ $item->estimated_hours }}h</span>
                                        @endif
                                        <button @click="starting=true; fetch('{{ route('admin.api.upskilling.sessions.start') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({learning_item_id:{{ $item->id }}})}).then(()=>location.href='{{ route('admin.dashboard.today') }}')"
                                            :disabled="starting"
                                            class="px-2 py-1 rounded-md border border-purple-200 bg-purple-50 text-purple-600 text-[10px] font-semibold cursor-pointer hover:bg-purple-100 transition shrink-0 disabled:opacity-50">▶ Start</button>
                                        <form method="POST" action="{{ route('admin.upskilling.items.complete', $item) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 rounded-md border border-green-200 bg-green-50 text-green-600 text-[10px] font-semibold cursor-pointer hover:bg-green-100 transition shrink-0">✓</button>
                                        </form>
                                        <span @click="openItemModal({{ $item->id }})" class="w-5 h-5 rounded border border-slate-200 bg-white flex items-center justify-center text-[10px] text-slate-500 cursor-pointer hover:bg-slate-50 shrink-0">✎</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-4 pl-12">
                                    <p class="text-xs text-slate-400">No pending items in this domain.</p>
                                </div>
                            @endif
                            <div class="px-4 py-2 pl-12 border-t border-slate-50">
                                <button @click="openItemModal(null, {{ $domain->id }})" class="text-[11px] font-semibold border-0 bg-transparent cursor-pointer hover:underline p-0" style="color: {{ $domain->hex_color }};">+ Add learning item</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
            <div class="admin-card p-8 text-center">
                <p class="text-sm text-slate-400">No skill domains yet. Create one to get started.</p>
            </div>
        @endif
    </div>

    {{-- Recent Sessions --}}
    <div class="mb-7">
        <h2 class="text-base font-bold text-slate-800 mb-3.5">Recent Sessions</h2>
        @if($recentSessions->count())
        <div class="admin-card overflow-hidden rounded-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-widest text-slate-400">Date</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-widest text-slate-400">Domain</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-widest text-slate-400">Item</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-widest text-slate-400">Duration</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-widest text-slate-400">Takeaway</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSessions as $session)
                            <tr class="border-b border-slate-50">
                                <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">{{ $session->session_date->format('j M') }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <span class="font-medium" style="color: {{ $session->skillDomain?->hex_color ?? '#94a3b8' }};">{{ $session->skillDomain?->icon_emoji }} {{ $session->skillDomain?->name ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-slate-800 max-w-[200px] truncate">{{ $session->learningItem?->title ?? '—' }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-slate-500">{{ $session->duration_minutes ? $session->duration_minutes . 'm' : '—' }}</td>
                                <td class="px-4 py-2.5 text-slate-500 max-w-[200px] truncate" title="{{ $session->takeaway }}">{{ $session->takeaway ? Str::limit($session->takeaway, 80) : '—' }}{{ $session->notes ? ' 📝' : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
            <div class="admin-card p-8 text-center">
                <p class="text-sm text-slate-400">No sessions logged yet.</p>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════
         Domain Modal
    ═══════════════════════════════════════════════ --}}
    <div x-show="showDomainModal" x-transition.opacity @click.self="showDomainModal = false" @keydown.escape.window="showDomainModal = false"
         class="fixed inset-0 bg-black/35 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div x-show="showDomainModal" x-transition class="bg-white rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">
            <h2 class="font-['Space_Grotesk'] text-lg font-bold text-slate-800 mb-5" x-text="domainEditId ? 'Edit Domain' : 'Add Domain'"></h2>
            <form :action="domainEditId ? '{{ url('admin/upskilling/domains') }}/' + domainEditId : '{{ route('admin.upskilling.domains.store') }}'" method="POST">
                @csrf
                <template x-if="domainEditId"><input type="hidden" name="_method" value="PATCH"></template>

                <div class="mb-3.5">
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Name *</label>
                    <input type="text" name="name" x-model="domainForm.name" required class="admin-input">
                </div>
                <div class="mb-3.5">
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Description</label>
                    <textarea name="description" x-model="domainForm.description" rows="2" class="admin-input resize-y"></textarea>
                </div>
                <div class="flex gap-3 mb-3.5">
                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Emoji</label>
                        <input type="text" name="icon_emoji" x-model="domainForm.icon_emoji" class="admin-input text-center text-xl">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Color</label>
                        <input type="color" name="hex_color" x-model="domainForm.hex_color" class="w-full h-[42px] rounded-lg border border-slate-200 p-1 cursor-pointer">
                    </div>
                </div>
                <div class="flex gap-3 mb-3.5">
                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Current Level (1–10)</label>
                        <input type="range" name="current_level" x-model="domainForm.current_level" min="1" max="10" class="w-full">
                        <span class="text-xs text-slate-500" x-text="domainForm.current_level"></span>
                    </div>
                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Target Level (1–10)</label>
                        <input type="range" name="target_level" x-model="domainForm.target_level" min="1" max="10" class="w-full">
                        <span class="text-xs text-slate-500" x-text="domainForm.target_level"></span>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="showDomainModal = false" class="px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-500 text-sm font-medium cursor-pointer hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 rounded-lg bg-slate-800 text-white text-sm font-semibold cursor-pointer hover:bg-slate-700 transition border-0" x-text="domainEditId ? 'Save' : 'Create'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         Item Modal
    ═══════════════════════════════════════════════ --}}
    <div x-show="showItemModal" x-transition.opacity @click.self="showItemModal = false" @keydown.escape.window="showItemModal = false"
         class="fixed inset-0 bg-black/35 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div x-show="showItemModal" x-transition class="bg-white rounded-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
            <h2 class="font-['Space_Grotesk'] text-lg font-bold text-slate-800 mb-5" x-text="itemEditId ? 'Edit Item' : 'Add Learning Item'"></h2>
            <form :action="itemEditId ? '{{ url('admin/upskilling/items') }}/' + itemEditId : '{{ route('admin.upskilling.items.store') }}'" method="POST">
                @csrf
                <template x-if="itemEditId"><input type="hidden" name="_method" value="PATCH"></template>

                <div class="mb-3.5">
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Domain *</label>
                    <select name="skill_domain_id" x-model="itemForm.skill_domain_id" required class="admin-input">
                        <option value="">Select domain</option>
                        @foreach($allDomains as $d)
                            <option value="{{ $d->id }}">{{ $d->icon_emoji }} {{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3.5">
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Title *</label>
                    <input type="text" name="title" x-model="itemForm.title" required class="admin-input" placeholder="e.g. Laravel 11 API Development — Laracasts">
                </div>
                <div class="mb-3.5">
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Type</label>
                    <div class="flex gap-1.5 flex-wrap">
                        @foreach($typeOptions as $t)
                            <button type="button" @click="itemForm.type = '{{ $t }}'"
                                :class="itemForm.type === '{{ $t }}' ? '{{ $typeColors[$t] }} border-transparent' : 'bg-white text-slate-500 border-slate-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium border cursor-pointer transition-all">{{ ucfirst($t) }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="type" :value="itemForm.type">
                </div>
                <div class="mb-3.5">
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Source URL</label>
                    <input type="text" name="source_url" x-model="itemForm.source_url" class="admin-input" placeholder="https://...">
                </div>
                <div class="flex gap-3 mb-3.5">
                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Est. Hours</label>
                        <input type="number" name="estimated_hours" x-model="itemForm.estimated_hours" step="0.5" min="0" class="admin-input" placeholder="e.g. 12">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Priority (1–10)</label>
                        <input type="range" name="priority" x-model="itemForm.priority" min="1" max="10" class="w-full">
                        <span class="text-xs text-slate-500" x-text="itemForm.priority"></span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Notes</label>
                    <textarea name="notes" x-model="itemForm.notes" rows="2" class="admin-input resize-y"></textarea>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="showItemModal = false" class="px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-500 text-sm font-medium cursor-pointer hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 rounded-lg bg-purple-500 text-white text-sm font-semibold cursor-pointer hover:bg-purple-600 transition border-0" x-text="itemEditId ? 'Save' : 'Add Item'"></button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    const domainsData = @js($domains->keyBy('id')->toArray());
    const itemsData = @js($pendingItems->keyBy('id')->toArray());

    function upskillingManager() {
        return {
            showDomainModal: false,
            showItemModal: false,
            domainEditId: null,
            itemEditId: null,
            domainForm: { name: '', description: '', icon_emoji: '📚', hex_color: '#a86fdf', current_level: 1, target_level: 8 },
            itemForm: { skill_domain_id: '', title: '', type: 'course', source_url: '', estimated_hours: '', priority: 5, notes: '' },

            openDomainModal(id = null) {
                this.domainEditId = id;
                if (id && domainsData[id]) {
                    const d = domainsData[id];
                    this.domainForm = {
                        name: d.name || '',
                        description: d.description || '',
                        icon_emoji: d.icon_emoji || '📚',
                        hex_color: d.hex_color || '#a86fdf',
                        current_level: d.current_level || 1,
                        target_level: d.target_level || 8,
                    };
                } else {
                    this.domainForm = { name: '', description: '', icon_emoji: '📚', hex_color: '#a86fdf', current_level: 1, target_level: 8 };
                }
                this.showDomainModal = true;
            },

            openItemModal(id = null, domainId = null) {
                this.itemEditId = id;
                if (id && itemsData[id]) {
                    const i = itemsData[id];
                    this.itemForm = {
                        skill_domain_id: i.skill_domain_id || '',
                        title: i.title || '',
                        type: i.type || 'course',
                        source_url: i.source_url || '',
                        estimated_hours: i.estimated_hours || '',
                        priority: i.priority || 5,
                        notes: i.notes || '',
                    };
                } else {
                    this.itemForm = { skill_domain_id: domainId || '', title: '', type: 'course', source_url: '', estimated_hours: '', priority: 5, notes: '' };
                }
                this.showItemModal = true;
            },
        };
    }
</script>
@endpush
@endsection
