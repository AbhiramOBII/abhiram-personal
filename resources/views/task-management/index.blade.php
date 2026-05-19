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
<div style="max-width: 900px; margin: 0 auto;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Task Management</h1>
            <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">All active tasks across your plans.</p>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            {{-- Range filter --}}
            <div style="display: flex; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb;">
                @foreach(['week' => 'This Week', '7days' => 'Last 7 Days', 'month' => 'This Month'] as $key => $label)
                    <a href="{{ route('admin.tasks.index', ['range' => $key, 'pillar' => $pillar]) }}"
                       style="padding: 8px 12px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.15s;
                       {{ $range === $key ? 'background: #1e293b; color: #fff;' : 'background: #fff; color: #64748b;' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            {{-- Pillar filter --}}
            <select onchange="window.location.href='{{ route('admin.tasks.index') }}?range={{ $range }}&pillar=' + this.value"
                    style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb; font-size: 12px; color: #1e293b; background: #fff; outline: none;">
                <option value="">All Pillars</option>
                @foreach($pillarList as $p)
                    <option value="{{ $p }}" {{ $pillar === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Task list grouped by date --}}
    @forelse($grouped as $date => $tasks)
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

            <div class="admin-card" style="overflow: hidden; border-radius: 12px;" x-data="{ expanded: {} }">
                @foreach($tasks as $task)
                    <div style="padding: 12px 14px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f8fafc;
                        {{ $task->is_rolled_over ? 'border-left: 3px solid #f59e0b;' : '' }}
                        {{ $task->is_completed ? 'opacity: 0.45;' : '' }}"
                         x-data="{ editing: false, title: '{{ addslashes($task->title) }}' }">

                        {{-- Priority dot --}}
                        <span style="width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: {{ $task->priority === 'must' ? '#ef4444' : ($task->priority === 'bonus' ? '#22c55e' : '#f59e0b') }};"></span>

                        {{-- Title (inline edit) --}}
                        <template x-if="!editing">
                            <span @click="editing = true" style="flex: 1; min-width: 0; font-size: 14px; font-weight: 500; color: #1e293b; cursor: text; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; {{ $task->is_completed ? 'text-decoration: line-through;' : '' }}" x-text="title"></span>
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

                        {{-- Due date --}}
                        @if($task->due_date)
                            <span style="font-size: 10px; font-weight: 600; white-space: nowrap; padding: 2px 6px; border-radius: 4px; {{ $task->isOverdue() ? 'background: #fef2f2; color: #dc2626;' : 'background: #f1f5f9; color: #64748b;' }}">{{ $task->due_date->format('j M') }}</span>
                        @endif

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
                                    <div style="padding: 8px 14px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; {{ $sub->is_completed ? 'opacity: 0.45;' : '' }}">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $sub->priority === 'must' ? '#ef4444' : ($sub->priority === 'bonus' ? '#22c55e' : '#f59e0b') }};"></span>
                                        <span style="flex: 1; font-size: 13px; color: #475569; {{ $sub->is_completed ? 'text-decoration: line-through;' : '' }}">{{ $sub->title }}</span>
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

</div>
@endsection
