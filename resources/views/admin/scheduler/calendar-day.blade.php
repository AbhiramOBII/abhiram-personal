@extends('admin.layouts.app')

@section('title', $date->format('D, M j Y'))

@section('content')
<div style="max-width: 960px;">

    <div style="margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #d0ad5d;">Scheduler</span>
            <span style="color: #cbd5e1;">→</span>
            <a href="{{ route('admin.scheduler.calendar', ['month' => $date->month, 'year' => $date->year]) }}" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #94a3b8; text-decoration: none;">Calendar</a>
            <span style="color: #cbd5e1;">→</span>
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #64748b;">{{ $date->format('M j') }}</span>
        </div>

        <div style="display: flex; align-items: center; gap: 14px;">
            @if($workingDay)
                <div style="width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0; background-color: {{ $workingDay->color ?? '#d0ad5d' }};"></div>
            @endif
            <div>
                <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">{{ $date->format('l, F j, Y') }}</h1>
                @if($workingDay)
                    <p style="margin-top: 4px; font-size: 14px; color: #94a3b8;">{{ $workingDay->theme }}</p>
                @endif
            </div>
        </div>

        @if($date->isToday())
            <div style="margin-top: 12px; display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; background: #fef8ec; border: 1px solid #fde68a;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #d0ad5d;"></div>
                <span style="font-size: 12px; font-weight: 600; color: #92700c;">Today</span>
            </div>
        @endif
    </div>

    <!-- Day Navigation -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <a href="{{ route('admin.scheduler.calendar.day', $date->copy()->subDay()->format('Y-m-d')) }}" class="admin-btn-outline" style="display: flex; align-items: center; gap: 6px; text-decoration: none;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $date->copy()->subDay()->format('M j') }}
        </a>

        @php
            $completedCount = $calendarDay ? $calendarDay->completedTasks()->count() : 0;
            $totalCount = $calendarDay ? $calendarDay->tasks()->count() : 0;
            $pct = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
        @endphp

        @if($totalCount > 0)
            <div style="text-align: center;">
                <div style="font-family: 'Space Grotesk', sans-serif; font-size: 24px; font-weight: 700; color: {{ $pct === 100 ? '#10b981' : '#1e293b' }};">{{ $pct }}%</div>
                <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">{{ $completedCount }}/{{ $totalCount }} done</div>
            </div>
        @endif

        <a href="{{ route('admin.scheduler.calendar.day', $date->copy()->addDay()->format('Y-m-d')) }}" class="admin-btn-outline" style="display: flex; align-items: center; gap: 6px; text-decoration: none;">
            {{ $date->copy()->addDay()->format('M j') }}
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 24px; padding: 14px 16px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div style="margin-bottom: 24px; padding: 14px 16px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
            <p style="font-size: 14px; color: #dc2626; margin: 0;">{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div style="margin-bottom: 24px; padding: 14px 16px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
            @foreach($errors->all() as $error)
                <p style="font-size: 14px; color: #dc2626; margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(!$isImported && $hasTemplate)
        <div class="admin-card" style="padding: 40px; text-align: center; margin-bottom: 32px;">
            <div style="width: 56px; height: 56px; margin: 0 auto 16px; border-radius: 50%; background: #fef8ec; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 28px; height: 28px; color: #d0ad5d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 8px 0;">Import {{ $workingDay->day_name }} Template</h3>
            <p style="font-size: 14px; color: #94a3b8; margin: 0 0 24px 0;">Load {{ $workingDay->timeSlots()->count() }} time slots from your <strong style="color: #64748b;">{{ $workingDay->theme }}</strong> template.</p>
            <form method="POST" action="{{ route('admin.scheduler.calendar.import', $date->format('Y-m-d')) }}">
                @csrf
                <button type="submit" class="admin-btn-gold" style="padding: 14px 28px; font-size: 14px;">Import Time Slots</button>
            </form>
        </div>
    @endif

    @if($calendarDay && $calendarDay->tasks->count())
        <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 32px;">
            @foreach($calendarDay->tasks as $task)
                <div class="admin-card" style="padding: 14px 18px; {{ $task->is_completed ? 'opacity: 0.5;' : '' }} transition: opacity 0.2s;">
                    <div style="display: flex; align-items: flex-start; gap: 14px;">

                        <form method="POST" action="{{ route('admin.scheduler.calendar.toggle-task', $task) }}" style="flex-shrink: 0; margin-top: 2px;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="width: 22px; height: 22px; border-radius: 6px; border: 2px solid {{ $task->is_completed ? '#10b981' : '#cbd5e1' }}; background: {{ $task->is_completed ? '#f0fdf4' : 'transparent' }}; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s;">
                                @if($task->is_completed)
                                    <svg style="width: 14px; height: 14px; color: #10b981;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </button>
                        </form>

                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
                                <span style="font-size: 12px; font-family: monospace; color: #94a3b8; flex-shrink: 0; width: 140px;">
                                    {{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($task->end_time)->format('h:i A') }}
                                </span>
                                <span style="font-size: 14px; font-weight: 500; color: {{ $task->is_completed ? '#94a3b8' : '#1e293b' }}; {{ $task->is_completed ? 'text-decoration: line-through;' : '' }}">
                                    {{ $task->description }}
                                </span>
                            </div>
                            <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                                <span class="admin-badge">{{ $task->pillar }}</span>
                                @if($task->notes)
                                    <span style="font-size: 11px; color: #94a3b8;">{{ Str::limit($task->notes, 40) }}</span>
                                @endif
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.scheduler.calendar.destroy-task', $task) }}" onsubmit="return confirm('Remove this task?')" style="flex-shrink: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #e5e7eb; transition: color 0.15s;">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="display: flex; justify-content: flex-end; margin-bottom: 32px;">
            <form method="POST" action="{{ route('admin.scheduler.calendar.clear', $date->format('Y-m-d')) }}" onsubmit="return confirm('Clear all tasks for this day? You can re-import the template.')">
                @csrf
                @method('DELETE')
                <button type="submit" style="font-size: 12px; color: #cbd5e1; background: none; border: none; cursor: pointer; transition: color 0.15s;">
                    Clear all tasks for this day
                </button>
            </form>
        </div>
    @elseif(!$hasTemplate)
        <div class="admin-card" style="padding: 40px; text-align: center; margin-bottom: 32px;">
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">No template available for {{ $date->format('l') }}. Add tasks manually below.</p>
        </div>
    @endif

    <div class="admin-card" style="padding: 24px; margin-bottom: 24px;">
        <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 16px; height: 16px; color: #d0ad5d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Task
        </h3>
        <form method="POST" action="{{ route('admin.scheduler.calendar.add-task', $date->format('Y-m-d')) }}" style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px;">
            @csrf
            <div>
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Start</label>
                <input type="time" name="start_time" required class="admin-input" style="width: 130px;">
            </div>
            <div>
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">End</label>
                <input type="time" name="end_time" required class="admin-input" style="width: 130px;">
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Description</label>
                <input type="text" name="description" required placeholder="What needs to happen..." class="admin-input">
            </div>
            <div style="width: 140px;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Pillar</label>
                <input type="text" name="pillar" required placeholder="Pillar..." class="admin-input">
            </div>
            <button type="submit" class="admin-btn-gold">Add</button>
        </form>
    </div>

    <div class="admin-card" style="padding: 24px;">
        <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 12px 0;">Day Notes</h3>
        <form method="POST" action="{{ route('admin.scheduler.calendar.notes', $date->format('Y-m-d')) }}">
            @csrf
            @method('PATCH')
            <textarea name="notes" rows="3" placeholder="Reflections, wins, blockers..." class="admin-input" style="resize: none; padding: 12px 14px;">{{ $calendarDay->notes ?? '' }}</textarea>
            <div style="margin-top: 12px; display: flex; justify-content: flex-end;">
                <button type="submit" class="admin-btn-outline">Save Notes</button>
            </div>
        </form>
    </div>

</div>
@endsection
