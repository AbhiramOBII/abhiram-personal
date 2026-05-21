@extends('admin.layouts.app')

@section('title', 'Task Management')

@php
    $pillarColors = [
        'revenue'=>'yellow','operations'=>'gray','marketing'=>'orange','growth'=>'lime',
        'content'=>'sky','creation'=>'purple','product'=>'indigo','networking'=>'blue',
        'community'=>'teal','media'=>'pink','brand'=>'rose','podcast'=>'green',
        'health'=>'red','recovery'=>'emerald','learning'=>'violet',
    ];
    $pillarList = ['revenue','operations','marketing','growth','content','creation','product','networking','community','media','brand','podcast','health','recovery','learning'];
    $dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
@endphp

@section('content')
<div style="max-width: 900px; margin: 0 auto;" x-data="dumpPage()">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Task Management</h1>
            <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">All active tasks across your plans.</p>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            {{-- Task Dump button --}}
            <button @click="openDump()" style="padding: 8px 14px; border-radius: 8px; border: 1px solid #e5e7eb; font-size: 12px; font-weight: 600; color: #1e293b; background: #fff; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                <svg style="width: 14px; height: 14px; color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                Task Dump
            </button>
            {{-- Bulk Upload button --}}
            <div x-data="{ showUpload: false }">
                <button @click="showUpload = true" style="padding: 8px 14px; border-radius: 8px; border: 1px solid #e5e7eb; font-size: 12px; font-weight: 600; color: #1e293b; background: #fff; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                    <svg style="width: 14px; height: 14px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Bulk Upload
                </button>

                {{-- Upload Modal --}}
                <div x-show="showUpload" x-cloak x-transition.opacity @click.self="showUpload = false"
                     style="position: fixed; inset: 0; background: rgba(15,23,42,0.4); backdrop-filter: blur(4px); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 16px;">
                    <div @click.stop style="background: #fff; border-radius: 16px; padding: 28px; width: 100%; max-width: 460px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                            <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">Bulk Upload Tasks</h3>
                            <button @click="showUpload = false" style="width: 28px; height: 28px; border-radius: 8px; border: none; background: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b;">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Format info --}}
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                            <p style="font-size: 12px; font-weight: 600; color: #475569; margin: 0 0 8px;">CSV Format (columns):</p>
                            <code style="font-size: 11px; color: #0f172a; background: #e2e8f0; padding: 3px 6px; border-radius: 4px; display: inline-block;">title, priority, pillar, estimated_minutes, date, time_block</code>
                            <ul style="margin: 10px 0 0; padding-left: 16px; font-size: 11px; color: #64748b; line-height: 1.8;">
                                <li><strong>title</strong> — required</li>
                                <li><strong>priority</strong> — must, should, or bonus (default: should)</li>
                                <li><strong>pillar</strong> — revenue, marketing, content, health, etc.</li>
                                <li><strong>estimated_minutes</strong> — integer</li>
                                <li><strong>date</strong> — YYYY-MM-DD (default: today)</li>
                                <li><strong>time_block</strong> — exact block name (optional)</li>
                            </ul>
                        </div>

                        {{-- Download sample --}}
                        <a href="{{ route('admin.tasks.sample-csv') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #4f98a3; text-decoration: none; margin-bottom: 16px;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download Sample CSV
                        </a>

                        {{-- Upload form --}}
                        <form method="POST" action="{{ route('admin.tasks.bulk-upload') }}" enctype="multipart/form-data">
                            @csrf
                            <label style="display: block; border: 2px dashed #e2e8f0; border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.borderColor='#4f98a3'; this.style.background='#f0fdfa'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='transparent'" x-data="{ fileName: '' }">
                                <input type="file" name="csv_file" accept=".csv,.txt" required style="display: none;" @change="fileName = $event.target.files[0]?.name || ''">
                                <div x-show="!fileName">
                                    <svg style="width: 24px; height: 24px; color: #94a3b8; margin: 0 auto 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p style="font-size: 13px; color: #64748b; margin: 0;">Click to select a .csv file</p>
                                </div>
                                <div x-show="fileName">
                                    <p style="font-size: 13px; color: #1e293b; font-weight: 600; margin: 0;" x-text="fileName"></p>
                                    <p style="font-size: 11px; color: #64748b; margin: 4px 0 0;">Click to change</p>
                                </div>
                            </label>
                            <button type="submit" style="width: 100%; margin-top: 14px; padding: 12px; border-radius: 10px; border: none; background: #1e293b; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#334155'" onmouseout="this.style.background='#1e293b'">
                                Upload & Import Tasks
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Type filter --}}
            <div style="display: flex; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb;">
                @foreach(['' => 'All', 'daily' => '⚡ Daily', 'project' => '🗂️ Project'] as $key => $label)
                    <a href="{{ route('admin.tasks.index', ['range' => $range, 'pillar' => $pillar, 'status' => $status, 'type' => $key]) }}"
                       style="padding: 8px 12px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.15s;
                       {{ ($type ?? '') === $key ? 'background: #1e293b; color: #fff;' : 'background: #fff; color: #64748b;' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            {{-- Range filter (hidden for project view) --}}
            @if(($type ?? '') !== 'project')
            <div style="display: flex; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb;">
                @foreach(['week' => 'This Week', '7days' => 'Last 7 Days', 'month' => 'This Month'] as $key => $label)
                    <a href="{{ route('admin.tasks.index', ['range' => $key, 'pillar' => $pillar, 'status' => $status, 'type' => $type]) }}"
                       style="padding: 8px 12px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.15s;
                       {{ $range === $key ? 'background: #1e293b; color: #fff;' : 'background: #fff; color: #64748b;' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            @endif
            {{-- Pillar filter --}}
            <select onchange="window.location.href='{{ route('admin.tasks.index') }}?range={{ $range }}&pillar=' + this.value + '&status={{ $status }}&type={{ $type }}'"
                    style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb; font-size: 12px; color: #1e293b; background: #fff; outline: none;">
                <option value="">All Pillars</option>
                @foreach($pillarList as $p)
                    <option value="{{ $p }}" {{ $pillar === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                @endforeach
            </select>
            {{-- Status filter --}}
            <div style="display: flex; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb;">
                <a href="{{ route('admin.tasks.index', ['range' => $range, 'pillar' => $pillar, 'type' => $type]) }}"
                   style="padding: 8px 10px; font-size: 11px; font-weight: 500; text-decoration: none; transition: all 0.15s;
                   {{ !$status ? 'background: #1e293b; color: #fff;' : 'background: #fff; color: #64748b;' }}">All</a>
                @foreach($statusConfig as $key => $cfg)
                    <a href="{{ route('admin.tasks.index', ['range' => $range, 'pillar' => $pillar, 'status' => $key, 'type' => $type]) }}"
                       style="padding: 8px 10px; font-size: 11px; font-weight: 500; text-decoration: none; transition: all 0.15s;
                       {{ $status === $key ? 'background: ' . $cfg['color'] . '; color: #fff;' : 'background: #fff; color: ' . $cfg['color'] . ';' }}">
                        {{ $cfg['emoji'] }} {{ $cfg['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
            <p style="font-size: 14px; color: #dc2626; margin: 0;">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #fffbeb; border: 1px solid #fde68a;">
            <p style="font-size: 12px; font-weight: 600; color: #92400e; margin: 0 0 6px;">Import warnings:</p>
            @foreach(session('import_errors') as $err)
                <p style="font-size: 12px; color: #92400e; margin: 2px 0;">• {{ $err }}</p>
            @endforeach
        </div>
    @endif

    @if($errors->any())
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
            @foreach($errors->all() as $error)
                <p style="font-size: 14px; color: #dc2626; margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Project Timeline View --}}
    @if(($type ?? '') === 'project' && $projectTimeline->isNotEmpty())
    <div style="margin-bottom: 24px;">
        @php
            $proximityLabels = ['overdue' => ['💀 Overdue', '#a12c7b'], '0d' => ['🔴 Due Today', '#a13544'], '1d' => ['🟠 Due Tomorrow', '#da7101'], '3d' => ['🟡 Due Soon (2-3 days)', '#d19900']];
        @endphp
        @foreach($proximityLabels as $pKey => $pInfo)
            @if($projectTimeline->has($pKey))
            <div style="margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; padding: 0 2px;">
                    <span style="font-size: 14px; font-weight: 700; color: {{ $pInfo[1] }};">{{ $pInfo[0] }}</span>
                    <span style="font-size: 11px; color: #94a3b8;">{{ $projectTimeline[$pKey]->count() }} tasks</span>
                </div>
                <div class="admin-card" style="overflow: visible; border-radius: 12px; border-left: 3px solid {{ $pInfo[1] }};">
                    @foreach($projectTimeline[$pKey] as $task)
                        @php $sc = $task->statusConfig; @endphp
                        <div style="padding: 12px 14px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f8fafc;">
                            <span style="padding: 2px 6px; border-radius: 5px; font-size: 10px; font-weight: 600; white-space: nowrap; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }};">{{ $sc['emoji'] }}</span>
                            <span style="width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: {{ $task->priority === 'must' ? '#ef4444' : ($task->priority === 'bonus' ? '#22c55e' : '#f59e0b') }};"></span>
                            <span style="flex: 1; min-width: 0; font-size: 14px; font-weight: 500; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $task->title }}</span>
                            @if($task->deadline_at)
                                <span style="font-size: 10px; font-weight: 600; white-space: nowrap; padding: 2px 6px; border-radius: 4px; background: {{ $pInfo[1] }}18; color: {{ $pInfo[1] }};">{{ $task->deadline_at->format('d M, g:i A') }}</span>
                            @endif
                            @if($task->pillar)
                                @php $pc = $pillarColors[$task->pillar] ?? 'gray'; @endphp
                                <span class="bg-{{ $pc }}-100 text-{{ $pc }}-700 border border-{{ $pc }}-200" style="padding: 1px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; white-space: nowrap;">{{ $task->pillar }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach

        {{-- Project tasks without urgent deadline --}}
        @if($projectTimeline->has(''))
        <div style="margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; padding: 0 2px;">
                <span style="font-size: 14px; font-weight: 700; color: #64748b;">📋 No Urgent Deadline</span>
                <span style="font-size: 11px; color: #94a3b8;">{{ $projectTimeline['']->count() }} tasks</span>
            </div>
            <div class="admin-card" style="overflow: visible; border-radius: 12px;">
                @foreach($projectTimeline[''] as $task)
                    @php $sc = $task->statusConfig; @endphp
                    <div style="padding: 12px 14px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f8fafc;">
                        <span style="padding: 2px 6px; border-radius: 5px; font-size: 10px; font-weight: 600; white-space: nowrap; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }};">{{ $sc['emoji'] }}</span>
                        <span style="flex: 1; min-width: 0; font-size: 14px; font-weight: 500; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $task->title }}</span>
                        @if($task->deadline_at)
                            <span style="font-size: 10px; font-weight: 600; white-space: nowrap; padding: 2px 6px; border-radius: 4px; background: #f1f5f9; color: #64748b;">{{ $task->deadline_at->format('d M') }}</span>
                        @else
                            <span style="font-size: 10px; color: #94a3b8;">No deadline</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Task list grouped by date --}}
    @forelse($grouped as $date => $tasks)
        @if($date === 'no-plan') @continue @endif
        @php
            $dateObj = \Carbon\Carbon::parse($date);
            $dayPlan = $tasks->first()?->dailyPlan;
            $wd = $dayPlan?->workingDay;
            $hex = $wd?->hex_color ?? '#64748b';
        @endphp
        <div style="margin-bottom: 24px;">
            {{-- Date header --}}
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; padding: 0 2px;">
                <span style="font-size: 15px; font-weight: 700; color: #1e293b;">{{ $dateObj->format('D, j M') }}</span>
                @if($wd)
                    <span style="padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: {{ $hex }}18; color: {{ $hex }}; border: 1px solid {{ $hex }}30;">{{ $wd->theme_short ?? $wd->theme }}</span>
                @endif
                <span style="font-size: 11px; color: #94a3b8;">{{ $tasks->count() }} tasks</span>
            </div>

            <div class="admin-card" style="overflow: visible; border-radius: 12px;" x-data="{ expanded: {} }">
                @foreach($tasks as $task)
                    <div style="padding: 12px 14px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f8fafc;
                        {{ $task->is_rolled_over ? 'border-left: 3px solid #f59e0b;' : '' }}
                        {{ $task->status === 'done' ? 'opacity: 0.45;' : '' }}"
                         x-data="{ editing: false, title: '{{ addslashes($task->title) }}' }">

                        {{-- Status badge --}}
                        @php $sc = $task->statusConfig; @endphp
                        <span style="padding: 2px 6px; border-radius: 5px; font-size: 10px; font-weight: 600; white-space: nowrap; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }};">{{ $sc['emoji'] }}</span>

                        {{-- Priority dot --}}
                        <span style="width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: {{ $task->priority === 'must' ? '#ef4444' : ($task->priority === 'bonus' ? '#22c55e' : '#f59e0b') }};"></span>

                        {{-- Title (inline edit) --}}
                        <template x-if="!editing">
                            <span @click="editing = true" style="flex: 1; min-width: 0; font-size: 14px; font-weight: 500; color: #1e293b; cursor: text; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; {{ $task->status === 'done' ? 'text-decoration: line-through;' : '' }}" x-text="title"></span>
                        </template>
                        <template x-if="editing">
                            <input type="text" x-model="title"
                                   @blur="editing = false; fetch('{{ url('admin/api/tasks') }}/{{ $task->id }}', { method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: JSON.stringify({title: title}) })"
                                   @keydown.enter="$event.target.blur()"
                                   style="flex: 1; min-width: 0; font-size: 14px; font-weight: 500; color: #1e293b; border: none; border-bottom: 1px solid #e5e7eb; outline: none; padding: 2px 0; background: transparent;"
                                   x-init="$nextTick(() => $el.focus())">
                        </template>

                        {{-- Pillar chip --}}
                        @if($task->pillar)
                            @php $pc = $pillarColors[$task->pillar] ?? 'gray'; @endphp
                            <span class="bg-{{ $pc }}-100 text-{{ $pc }}-700 border border-{{ $pc }}-200" style="padding: 1px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; white-space: nowrap;">{{ $task->pillar }}</span>
                        @endif

                        {{-- TBCB (To Be Completed By) --}}
                        <div x-data="{ tbcbOpen: false, tbcbDate: '{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}' }" style="position: relative; display: inline-flex;">
                            <button @click="tbcbOpen = !tbcbOpen"
                                    style="padding: 2px 7px; border-radius: 6px; font-size: 10px; font-weight: 700; white-space: nowrap; border: 1px solid {{ $task->due_date ? ($task->isOverdue() ? '#fca5a5' : '#c7d2fe') : '#e5e7eb' }}; cursor: pointer; display: inline-flex; align-items: center; gap: 3px; transition: all 0.15s;
                                    {{ $task->due_date ? ($task->isOverdue() ? 'background: #fef2f2; color: #dc2626;' : 'background: #eef2ff; color: #4f46e5;') : 'background: #f8fafc; color: #94a3b8;' }}"
                                    title="To Be Completed By">
                                <svg style="width: 10px; height: 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span x-text="tbcbDate ? 'TBCB ' + new Date(tbcbDate + 'T00:00:00').toLocaleDateString('en-IN', {day:'numeric', month:'short'}) : 'TBCB'"></span>
                            </button>
                            <div x-show="tbcbOpen" x-cloak @click.outside="tbcbOpen = false"
                                 style="position: absolute; right: 0; top: 26px; z-index: 50; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); padding: 10px; min-width: 170px;">
                                <p style="font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.6px; margin: 0 0 6px 2px;">To Be Completed By</p>
                                <input type="date" x-model="tbcbDate"
                                       style="width: 100%; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 8px; color: #1e293b; outline: none; cursor: pointer;"
                                       @change="
                                           fetch('{{ url('admin/api/tasks') }}/{{ $task->id }}', {
                                               method: 'PATCH',
                                               headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                               body: JSON.stringify({ due_date: $event.target.value || null })
                                           }).then(r => { if(r.ok) tbcbOpen = false; });
                                       ">
                                <button x-show="tbcbDate" @click="
                                    tbcbDate = '';
                                    fetch('{{ url('admin/api/tasks') }}/{{ $task->id }}', {
                                        method: 'PATCH',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                        body: JSON.stringify({ due_date: null })
                                    }).then(r => { if(r.ok) tbcbOpen = false; });
                                " style="width: 100%; margin-top: 6px; padding: 4px; border-radius: 6px; border: 1px solid #fecaca; background: #fef2f2; color: #dc2626; font-size: 10px; font-weight: 600; cursor: pointer;">Clear date</button>
                            </div>
                        </div>

                        {{-- Rollover badge --}}
                        @if($task->is_rolled_over)
                            <span style="padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; background: #fef3c7; color: #92400e; white-space: nowrap;">↩ {{ $task->rollover_count }}</span>
                        @endif

                        {{-- Recurring icon --}}
                        @if($task->is_recurring)
                            <span style="color: #8b5cf6;" title="Recurring">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </span>
                        @endif

                        {{-- Sub-task count --}}
                        @if($task->subTasks->count() > 0)
                            <button @click="expanded[{{ $task->id }}] = !expanded[{{ $task->id }}]" style="padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; background: #e0f2fe; color: #0369a1; border: none; cursor: pointer; white-space: nowrap;">
                                {{ $task->subTasks->count() }} sub
                            </button>
                        @endif

                        {{-- Move to day --}}
                        <div x-data="{ moving: false }" style="position: relative; display: inline-flex;">
                            <button @click="moving = !moving" title="Move to another day" style="width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </button>
                            <div x-show="moving" x-cloak @click.outside="moving = false"
                                 style="position: absolute; right: 0; top: 30px; z-index: 50; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); padding: 8px; min-width: 150px;">
                                <p style="font-size: 10px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 6px 4px;">Move to</p>
                                <input type="date"
                                       min="{{ now()->toDateString() }}"
                                       style="width: 100%; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 8px; color: #1e293b; outline: none; cursor: pointer;"
                                       @change="
                                           fetch('{{ url('admin/api/tasks') }}/{{ $task->id }}/reassign', {
                                               method: 'POST',
                                               headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                               body: JSON.stringify({ date: $event.target.value })
                                           })
                                           .then(r => r.json())
                                           .then(d => { if(d.success) window.location.reload(); })
                                           .catch(() => alert('Failed to move task'));
                                           moving = false;
                                       ">
                            </div>
                        </div>

                        {{-- Archive --}}
                        <form method="POST" action="{{ route('admin.api.tasks.archive', $task) }}" style="display: inline;">
                            @csrf
                            <button type="submit" title="Archive" style="width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #94a3b8;">
                                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            </button>
                        </form>
                    </div>

                    {{-- Sub-tasks (expandable) --}}
                    @if($task->subTasks->count() > 0)
                        <template x-if="expanded[{{ $task->id }}]">
                            <div style="padding-left: 32px; background: #fafbfc;">
                                @foreach($task->subTasks as $sub)
                                    <div style="padding: 8px 14px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; {{ $sub->status === 'done' ? 'opacity: 0.45;' : '' }}">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $sub->priority === 'must' ? '#ef4444' : ($sub->priority === 'bonus' ? '#22c55e' : '#f59e0b') }};"></span>
                                        <span style="flex: 1; font-size: 13px; color: #475569; {{ $sub->status === 'done' ? 'text-decoration: line-through;' : '' }}">{{ $sub->title }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </template>
                    @endif
                @endforeach
            </div>
        </div>
    @empty
        <div class="admin-card" style="padding: 48px 24px; text-align: center;">
            <p style="font-size: 15px; color: #94a3b8; margin: 0;">No tasks yet — add your first task from <a href="{{ route('admin.dashboard.today') }}" style="color: #1e293b; font-weight: 600;">Today's Dashboard</a>.</p>
        </div>
    @endforelse

{{-- ═══════════════════════════════════════════════
     BRAIN DUMP MODAL
═══════════════════════════════════════════════ --}}
<div
    x-show="modalOpen" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    style="position: fixed; inset: 0; z-index: 200; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);"
    @keydown.escape.window="maybeClose()"
    @keydown.meta.enter.window="handleMetaEnter()"
    @keydown.ctrl.enter.window="handleMetaEnter()"
>
    {{-- Modal Panel --}}
    <div
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        @click.stop
        style="position: relative; display: flex; flex-direction: column; width: 100%; max-width: 760px; height: 90vh; background: #fff; border-radius: 16px; box-shadow: 0 24px 80px rgba(0,0,0,0.25); overflow: hidden;"
    >

        {{-- ▸ CAPTURING STATE --}}
        <template x-if="state === 'capturing' || state === 'loading'">
            <div style="display: flex; flex-direction: column; height: 100%; padding: 24px; gap: 16px;">
                {{-- Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 20px; height: 20px; color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            Brain Dump
                        </h2>
                        <p style="font-size: 12px; color: #94a3b8; margin: 4px 0 0;">Type one task per line. Don't think. Just capture.</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 12px; color: #94a3b8;" x-text="lines.length + ' task' + (lines.length !== 1 ? 's' : '')"></span>
                        <button @click="maybeClose()" style="width: 28px; height: 28px; border-radius: 8px; border: none; background: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                {{-- Textarea --}}
                <textarea
                    x-ref="dumpTextarea"
                    x-model="rawText"
                    :disabled="state === 'loading'"
                    placeholder="Follow up with Rajan about website quote&#10;Record podcast intro for episode 14&#10;Fix the rollover bug in DayOS&#10;Read chapter 3 of Never Split the Difference&#10;Post Monday LinkedIn content&#10;Call accountant about GST filing&#10;..."
                    style="flex: 1; resize: none; padding: 16px; outline: none; background: #f8fafc; border-radius: 10px; font-size: 14px; line-height: 2.0; color: #1e293b; border: 1px solid #e2e8f0;"
                ></textarea>
                {{-- Loading bar --}}
                <div x-show="state === 'loading'" style="width: 100%; height: 2px; background: #f1f5f9; border-radius: 999px; overflow: hidden;">
                    <div style="height: 100%; width: 40%; background: #7c3aed; animation: dumpLoadingBar 1.5s ease-in-out infinite;"></div>
                </div>
                {{-- Footer --}}
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <button x-show="rawText.trim().length > 0 && state === 'capturing'" @click="clearDump()" style="font-size: 12px; color: #94a3b8; background: none; border: none; cursor: pointer;">Clear all</button>
                        <span x-show="state === 'loading'" style="font-size: 12px; color: #94a3b8;" x-text="currentMessage"></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span x-show="state === 'capturing'" style="font-size: 11px; color: #cbd5e1;">Ctrl+Enter to organise</span>
                        <button
                            x-show="state === 'capturing'"
                            @click="categorise()"
                            :disabled="lines.length === 0"
                            :style="lines.length === 0 ? 'opacity: 0.4; cursor: not-allowed;' : ''"
                            style="padding: 10px 20px; border-radius: 10px; border: none; background: #7c3aed; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.15s;"
                        >
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            Organise with AI
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ▸ REVIEWING STATE --}}
        <template x-if="state === 'reviewing'">
            <div style="display: flex; flex-direction: column; height: 100%; gap: 0;">
                {{-- Header --}}
                <div style="padding: 18px 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Review Tasks</h2>
                        <p style="font-size: 12px; color: #94a3b8; margin: 2px 0 0;" x-text="tasks.length + ' tasks categorised — edit anything below'"></p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button @click="state = 'capturing'" style="padding: 6px 14px; font-size: 12px; color: #64748b; background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer;">&larr; Back</button>
                        <button @click="maybeClose()" style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Table --}}
                <div style="flex: 1; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e5e7eb; position: sticky; top: 0; z-index: 1;">
                                <th style="padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; width: 36px;">
                                    <input type="checkbox" :checked="selectedTasks.length === tasks.length" @change="tasks.forEach(t => t.selected = $event.target.checked)" style="cursor: pointer; accent-color: #7c3aed;">
                                </th>
                                <th style="padding: 8px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Task</th>
                                <th style="padding: 8px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; width: 110px;">Pillar</th>
                                <th style="padding: 8px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; width: 85px;">Priority</th>
                                <th style="padding: 8px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; width: 75px;">Day</th>
                                <th style="padding: 8px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; width: 65px;">Time</th>
                                <th style="padding: 8px 8px; width: 30px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(task, index) in tasks" :key="index">
                                <tr
                                    style="border-bottom: 1px solid #f1f5f9; transition: all 0.1s;"
                                    :style="!task.selected ? 'opacity: 0.4; background: #fafbfc;' : ''"
                                    onmouseover="this.style.background=this.style.opacity==='0.4'?'#fafbfc':'#f8fafc'"
                                    onmouseout="this.style.background=this.style.opacity==='0.4'?'#fafbfc':''"
                                >
                                    <td style="padding: 10px 12px; vertical-align: middle;">
                                        <input type="checkbox" x-model="task.selected" style="cursor: pointer; accent-color: #7c3aed;">
                                    </td>
                                    <td style="padding: 10px 8px; vertical-align: middle;">
                                        <input type="text" x-model="task.title" style="width: 100%; font-size: 13px; font-weight: 500; color: #1e293b; border: none; background: transparent; outline: none; padding: 2px 0;">
                                    </td>
                                    <td style="padding: 10px 8px; vertical-align: middle;">
                                        <select x-model="task.pillar" style="width: 100%; font-size: 12px; color: #475569; border: 1px solid #e5e7eb; border-radius: 4px; padding: 4px 6px; background: #fff; outline: none; cursor: pointer;">
                                            <option value="revenue">Revenue</option>
                                            <option value="marketing">Marketing</option>
                                            <option value="creation">Creation</option>
                                            <option value="networking">Networking</option>
                                            <option value="learning">Learning</option>
                                            <option value="recovery">Recovery</option>
                                            <option value="operations">Operations</option>
                                            <option value="personal">Personal</option>
                                        </select>
                                    </td>
                                    <td style="padding: 10px 8px; vertical-align: middle;">
                                        <select x-model="task.priority" style="width: 100%; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 4px 6px; background: #fff; outline: none; cursor: pointer;" :style="'color:' + priorityColor(task.priority)">
                                            <option value="must" style="color: #ef4444;">Must</option>
                                            <option value="should" style="color: #f59e0b;">Should</option>
                                            <option value="bonus" style="color: #22c55e;">Bonus</option>
                                        </select>
                                    </td>
                                    <td style="padding: 10px 8px; vertical-align: middle;">
                                        <select x-model="task.suggested_day" style="width: 100%; font-size: 12px; color: #475569; border: 1px solid #e5e7eb; border-radius: 4px; padding: 4px 6px; background: #fff; outline: none; cursor: pointer;">
                                            <option value="sunday">Sun</option>
                                            <option value="monday">Mon</option>
                                            <option value="tuesday">Tue</option>
                                            <option value="wednesday">Wed</option>
                                            <option value="thursday">Thu</option>
                                            <option value="friday">Fri</option>
                                            <option value="saturday">Sat</option>
                                        </select>
                                    </td>
                                    <td style="padding: 10px 8px; vertical-align: middle;">
                                        <select x-model="task.estimated_minutes" style="width: 100%; font-size: 12px; color: #475569; border: 1px solid #e5e7eb; border-radius: 4px; padding: 4px 6px; background: #fff; outline: none; cursor: pointer;">
                                            <option value="15">15m</option>
                                            <option value="30">30m</option>
                                            <option value="45">45m</option>
                                            <option value="60">1h</option>
                                            <option value="90">1.5h</option>
                                            <option value="120">2h</option>
                                        </select>
                                    </td>
                                    <td style="padding: 10px 8px; vertical-align: middle;">
                                        <button @click="tasks.splice(index, 1)" style="background: none; border: none; cursor: pointer; color: #cbd5e1; padding: 2px;" title="Remove">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div style="padding: 14px 24px; border-top: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 12px; color: #64748b;" x-text="selectedTasks.length + ' of ' + tasks.length + ' selected'"></span>
                    <button
                        @click="confirmTasks()"
                        :disabled="selectedTasks.length === 0"
                        :style="selectedTasks.length === 0 ? 'opacity: 0.4; cursor: not-allowed;' : ''"
                        style="padding: 10px 20px; border-radius: 8px; border: none; background: #1e293b; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;"
                        x-text="'Confirm ' + selectedTasks.length + ' Task' + (selectedTasks.length !== 1 ? 's' : '')"
                    ></button>
                </div>
            </div>
        </template>

        {{-- ▸ SUCCESS STATE --}}
        <template x-if="state === 'success'">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 24px; padding: 24px; text-align: center;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #f0fdf4; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 32px; height: 32px; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;" x-text="createdCount + ' task' + (createdCount !== 1 ? 's' : '') + ' added'"></h2>
                    <p style="font-size: 13px; color: #94a3b8; margin: 8px 0 0;">They've been assigned to the right days based on your themes.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button @click="reset()" style="padding: 10px 18px; border-radius: 10px; border: 1px solid #e2e8f0; background: #fff; color: #1e293b; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <svg style="width: 14px; height: 14px; color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        Dump More
                    </button>
                    <button @click="closeModal(); window.location.reload()" style="padding: 10px 18px; border-radius: 10px; border: none; background: #1e293b; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;">View Tasks</button>
                </div>
            </div>
        </template>

    </div>
</div>

</div> {{-- close x-data="dumpPage()" --}}
@endsection

@push('head')
<style>
    @keyframes dumpLoadingBar {
        0%   { transform: translateX(-100%); }
        50%  { transform: translateX(250%); }
        100% { transform: translateX(-100%); }
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
function dumpPage() {
    return {
        modalOpen: false,
        state: 'capturing',
        rawText: '',
        tasks: [],
        createdCount: 0,
        currentMessage: 'Reading your tasks...',
        _msgInterval: null,

        get lines() {
            return this.rawText.split('\n').filter(l => l.trim().length > 2);
        },
        get selectedTasks() {
            return this.tasks.filter(t => t.selected);
        },

        openDump() {
            this.modalOpen = true;
            this.$nextTick(() => {
                if (this.$refs.dumpTextarea) this.$refs.dumpTextarea.focus();
            });
        },
        maybeClose() {
            if (this.rawText.trim().length > 0 && this.state === 'capturing') {
                if (!window.confirm('You have unsaved tasks. Close anyway?')) return;
            }
            this.closeModal();
        },
        closeModal() {
            this.modalOpen = false;
            this.stopLoadingMessages();
        },
        clearDump() {
            if (!window.confirm('Clear all? This cannot be undone.')) return;
            this.rawText = '';
        },

        async categorise() {
            if (this.lines.length === 0) return;
            this.state = 'loading';
            this.startLoadingMessages();
            try {
                const res = await fetch('{{ route("admin.api.dump.categorise") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ lines: this.lines })
                });
                if (!res.ok) throw new Error('API error');
                const data = await res.json();
                this.tasks = data.map(t => ({ ...t, selected: true, estimated_minutes: String(t.estimated_minutes) }));
                this.state = 'reviewing';
            } catch (e) {
                this.state = 'capturing';
                alert('AI categorisation failed. Please try again.');
            }
            this.stopLoadingMessages();
        },

        async confirmTasks() {
            if (this.selectedTasks.length === 0) return;
            try {
                const payload = this.selectedTasks.map(t => ({
                    ...t,
                    estimated_minutes: parseInt(t.estimated_minutes) || 30
                }));
                const res = await fetch('{{ route("admin.api.dump.confirm") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ tasks: payload })
                });
                const data = await res.json();
                if (data.success) {
                    this.createdCount = data.created;
                    this.state = 'success';
                }
            } catch (e) {
                alert('Failed to save tasks. Please try again.');
            }
        },

        reset() {
            this.rawText = '';
            this.tasks = [];
            this.createdCount = 0;
            this.state = 'capturing';
            this.$nextTick(() => {
                if (this.$refs.dumpTextarea) this.$refs.dumpTextarea.focus();
            });
        },

        handleMetaEnter() {
            if (!this.modalOpen) return;
            if (this.state === 'capturing') this.categorise();
            if (this.state === 'reviewing') this.confirmTasks();
        },

        pillarColor(pillar) {
            const c = {
                revenue: '#d19900', marketing: '#da7101', creation: '#7c3aed',
                networking: '#0369a1', learning: '#437a22', recovery: '#0d9488',
                operations: '#64748b', personal: '#a16207'
            };
            return c[pillar] || '#64748b';
        },
        priorityColor(priority) {
            const c = { must: '#ef4444', should: '#f59e0b', bonus: '#22c55e' };
            return c[priority] || '#94a3b8';
        },

        loadingMessages: [
            'Reading your tasks...',
            'Identifying pillars...',
            'Matching to your day themes...',
            'Estimating durations...',
            'Almost there...'
        ],
        startLoadingMessages() {
            let i = 0;
            this.currentMessage = this.loadingMessages[0];
            this._msgInterval = setInterval(() => {
                i = (i + 1) % this.loadingMessages.length;
                this.currentMessage = this.loadingMessages[i];
            }, 1500);
        },
        stopLoadingMessages() {
            if (this._msgInterval) clearInterval(this._msgInterval);
        }
    };
}
</script>
@endpush
