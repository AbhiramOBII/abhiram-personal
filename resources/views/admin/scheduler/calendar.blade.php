@extends('admin.layouts.app')

@section('title', 'Calendar')

@section('content')
<div style="max-width: 960px;">

    <div style="margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #d0ad5d;">Scheduler</span>
            <span style="color: #cbd5e1;">→</span>
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #94a3b8;">Calendar</span>
        </div>
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">Calendar</h1>
        <p style="margin-top: 8px; font-size: 14px; color: #94a3b8;">Click a date to view, import, and check off your daily schedule.</p>
    </div>

    @php
        $prevMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();
        $today = now()->toDateString();
    @endphp

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <a href="{{ route('admin.scheduler.calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="admin-btn-outline" style="display: flex; align-items: center; gap: 6px; text-decoration: none;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $prevMonth->format('M') }}
        </a>
        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">{{ $startOfMonth->format('F Y') }}</h2>
        <a href="{{ route('admin.scheduler.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="admin-btn-outline" style="display: flex; align-items: center; gap: 6px; text-decoration: none;">
            {{ $nextMonth->format('M') }}
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="admin-card" style="overflow: hidden; padding: 0;">
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); border-bottom: 1px solid #e5e7eb;">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayLabel)
                <div style="padding: 10px 8px; text-align: center;">
                    <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8;">{{ $dayLabel }}</span>
                </div>
            @endforeach
        </div>

        @php
            $startDow = $startOfMonth->dayOfWeek;
            $daysInMonth = $startOfMonth->daysInMonth;
            $totalCells = ceil(($startDow + $daysInMonth) / 7) * 7;
        @endphp

        <div style="display: grid; grid-template-columns: repeat(7, 1fr);">
            @for($i = 0; $i < $totalCells; $i++)
                @php
                    $dayNum = $i - $startDow + 1;
                    $isValidDay = $dayNum >= 1 && $dayNum <= $daysInMonth;
                    $cellDate = $isValidDay ? $startOfMonth->copy()->day($dayNum) : null;
                    $dateKey = $cellDate ? $cellDate->format('Y-m-d') : null;
                    $calDay = $dateKey ? ($calendarDays[$dateKey] ?? null) : null;
                    $isToday = $dateKey === $today;
                    $wdColor = null;
                    if ($cellDate) {
                        $wd = \App\Models\WorkingDay::where('day_number', $cellDate->dayOfWeek)->first();
                        $wdColor = $wd ? $wd->color : null;
                    }
                    $taskCount = $calDay ? $calDay->tasks_count : 0;
                    $completedCount = $calDay ? $calDay->completed_tasks_count : 0;
                    $pct = $taskCount > 0 ? round(($completedCount / $taskCount) * 100) : 0;
                    $isPast = $cellDate && $cellDate->format('Y-m-d') < $today;
                @endphp

                <div style="min-height: 90px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; {{ !$isValidDay ? 'background: #fafbfc;' : '' }} {{ $isToday ? 'background: #fffbeb;' : '' }} {{ $isPast ? 'background: #fafbfc; opacity: 0.4;' : '' }}">
                    @if($isValidDay && !$isPast)
                        <a href="{{ route('admin.scheduler.calendar.day', $cellDate->format('Y-m-d')) }}" style="display: block; height: 100%; padding: 8px; text-decoration: none; transition: background 0.15s;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                                <span style="font-size: 13px; font-weight: {{ $isToday ? '700' : '500' }}; color: {{ $isToday ? '#d0ad5d' : '#475569' }};">{{ $dayNum }}</span>
                                @if($wdColor)
                                    <div style="width: 6px; height: 6px; border-radius: 50%; opacity: 0.5; background-color: {{ $wdColor }};"></div>
                                @endif
                            </div>
                            @if($taskCount > 0)
                                <div style="margin-top: 4px;">
                                    <div style="height: 3px; border-radius: 3px; background: #e5e7eb; overflow: hidden;">
                                        <div style="height: 100%; border-radius: 3px; background: {{ $pct === 100 ? '#10b981' : '#d0ad5d' }}; width: {{ $pct }}%; transition: width 0.5s;"></div>
                                    </div>
                                    <div style="margin-top: 3px; display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 9px; color: #94a3b8;">{{ $completedCount }}/{{ $taskCount }}</span>
                                        @if($pct === 100)
                                            <svg style="width: 12px; height: 12px; color: #10b981;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </a>
                    @elseif($isValidDay && $isPast)
                        <div style="padding: 8px;">
                            <span style="font-size: 13px; font-weight: 500; color: #cbd5e1;">{{ $dayNum }}</span>
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <div style="margin-top: 24px; display: flex; flex-wrap: wrap; align-items: center; gap: 24px; font-size: 12px; color: #94a3b8;">
        <div style="display: flex; align-items: center; gap: 6px;">
            <div style="width: 12px; height: 3px; border-radius: 3px; background: #d0ad5d;"></div>
            <span>In progress</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <div style="width: 12px; height: 3px; border-radius: 3px; background: #10b981;"></div>
            <span>Completed</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <div style="width: 12px; height: 12px; border-radius: 50%; background: #fffbeb; border: 1px solid #fde68a;"></div>
            <span>Today</span>
        </div>
    </div>

</div>
@endsection
