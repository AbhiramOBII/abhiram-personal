@extends('admin.layouts.app')

@section('title', "Today's Tasks")

@section('content')
<div style="max-width: 720px; margin: 0 auto;">

    <!-- Header -->
    <div style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            @if($workingDay)
                <div style="width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; background-color: {{ $workingDay->color ?? '#d0ad5d' }};"></div>
            @endif
            <div style="flex: 1; min-width: 0;">
                <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Today's Tasks</h1>
                <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">
                    {{ $today->format('l, M j') }}
                    @if($workingDay && $workingDay->theme)
                        · <span style="color: #d0ad5d;">{{ $workingDay->theme }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Progress -->
    @if($totalCount > 0)
        <div class="admin-card" style="padding: 16px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; font-weight: 600; color: #64748b;">Progress</span>
                <span style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; color: {{ $pct === 100 ? '#10b981' : '#1e293b' }};">{{ $pct }}%</span>
            </div>
            <div style="height: 6px; border-radius: 6px; background: #e5e7eb; overflow: hidden;">
                <div style="height: 100%; border-radius: 6px; background: {{ $pct === 100 ? '#10b981' : '#d0ad5d' }}; width: {{ $pct }}%; transition: width 0.5s;"></div>
            </div>
            <div style="margin-top: 6px; font-size: 12px; color: #94a3b8;">{{ $completedCount }} of {{ $totalCount }} done</div>
        </div>
    @endif

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
    @if($errors->any())
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
            @foreach($errors->all() as $error)
                <p style="font-size: 14px; color: #dc2626; margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Import -->
    @if(!$isImported && $hasTemplate)
        <div class="admin-card" style="padding: 32px 20px; text-align: center; margin-bottom: 16px;">
            <div style="width: 48px; height: 48px; margin: 0 auto 12px; border-radius: 50%; background: #fef8ec; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 24px; height: 24px; color: #d0ad5d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 6px 0;">Import {{ $workingDay->day_name }} Template</h3>
            <p style="font-size: 13px; color: #94a3b8; margin: 0 0 20px 0;">{{ $workingDay->timeSlots()->count() }} slots · <strong style="color: #64748b;">{{ $workingDay->theme }}</strong></p>
            <form method="POST" action="{{ route('admin.scheduler.calendar.import', $today->format('Y-m-d')) }}">
                @csrf
                <button type="submit" class="admin-btn-gold" style="width: 100%; padding: 14px; font-size: 15px;">Import Time Slots</button>
            </form>
        </div>
    @endif

    <!-- Task List -->
    @if($tasks->count())
        <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px;">
            @foreach($tasks as $task)
                <div class="admin-card" style="padding: 14px 12px; {{ $task->is_completed ? 'opacity: 0.4;' : '' }} transition: opacity 0.2s;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <!-- Checkbox -->
                        <form method="POST" action="{{ route('admin.scheduler.calendar.toggle-task', $task) }}" style="flex-shrink: 0; margin-top: 1px;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="width: 28px; height: 28px; border-radius: 8px; border: 2px solid {{ $task->is_completed ? '#10b981' : '#cbd5e1' }}; background: {{ $task->is_completed ? '#f0fdf4' : 'transparent' }}; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; -webkit-appearance: none;">
                                @if($task->is_completed)
                                    <svg style="width: 16px; height: 16px; color: #10b981;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </button>
                        </form>

                        <!-- Content -->
                        <div style="flex: 1; min-width: 0;">
                            <span style="font-size: 15px; font-weight: 500; color: {{ $task->is_completed ? '#94a3b8' : '#1e293b' }}; {{ $task->is_completed ? 'text-decoration: line-through;' : '' }} display: block; line-height: 1.4;">
                                {{ $task->description }}
                            </span>
                            <div style="margin-top: 4px; display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                                <span style="font-size: 11px; font-family: monospace; color: #94a3b8;">
                                    {{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($task->end_time)->format('h:i A') }}
                                </span>
                                <span class="admin-badge">{{ $task->pillar }}</span>
                            </div>
                        </div>

                        <!-- Delete -->
                        <form method="POST" action="{{ route('admin.scheduler.calendar.destroy-task', $task) }}" onsubmit="return confirm('Remove this task?')" style="flex-shrink: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: none; border: none; cursor: pointer; color: #e5e7eb; border-radius: 6px;">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Clear -->
        <div style="text-align: center; margin-bottom: 20px;">
            <form method="POST" action="{{ route('admin.scheduler.calendar.clear', $today->format('Y-m-d')) }}" onsubmit="return confirm('Clear all tasks for today? You can re-import the template.')">
                @csrf
                @method('DELETE')
                <button type="submit" style="font-size: 12px; color: #cbd5e1; background: none; border: none; cursor: pointer; padding: 8px 16px;">
                    Clear all tasks
                </button>
            </form>
        </div>
    @elseif(!$hasTemplate)
        <div class="admin-card" style="padding: 32px 20px; text-align: center; margin-bottom: 16px;">
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">No template for {{ $today->format('l') }}. Add tasks below.</p>
        </div>
    @endif

    <!-- Add Task -->
    <div class="admin-card" style="padding: 16px; margin-bottom: 16px;">
        <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 16px; height: 16px; color: #d0ad5d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Task
        </h3>
        <form method="POST" action="{{ route('admin.scheduler.calendar.add-task', $today->format('Y-m-d')) }}">
            @csrf
            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Start</label>
                    <input type="time" name="start_time" required class="admin-input">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">End</label>
                    <input type="time" name="end_time" required class="admin-input">
                </div>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Description</label>
                <input type="text" name="description" required placeholder="What needs to happen..." class="admin-input">
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px;">Pillar</label>
                <input type="text" name="pillar" required placeholder="e.g. Revenue, Ops..." class="admin-input">
            </div>
            <button type="submit" class="admin-btn-gold" style="width: 100%; padding: 14px;">Add Task</button>
        </form>
    </div>

    <!-- Day Notes -->
    <div class="admin-card" style="padding: 16px;">
        <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 10px 0;">Notes</h3>
        <form method="POST" action="{{ route('admin.scheduler.calendar.notes', $today->format('Y-m-d')) }}">
            @csrf
            @method('PATCH')
            <textarea name="notes" rows="3" placeholder="Reflections, wins, blockers..." class="admin-input" style="resize: none; padding: 12px 14px;">{{ $calendarDay->notes ?? '' }}</textarea>
            <div style="margin-top: 10px;">
                <button type="submit" class="admin-btn-outline" style="width: 100%; padding: 12px;">Save Notes</button>
            </div>
        </form>
    </div>

</div>
@endsection
