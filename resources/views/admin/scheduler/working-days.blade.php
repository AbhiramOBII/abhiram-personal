@extends('admin.layouts.app')

@section('title', 'Working Days')

@section('content')
<div style="max-width: 960px;">

    <div style="margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #d0ad5d;">Scheduler</span>
            <span style="color: #cbd5e1;">→</span>
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #94a3b8;">Working Days</span>
        </div>
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">Weekly Theme Schedule</h1>
        <p style="margin-top: 8px; font-size: 14px; color: #94a3b8;">Each day has a dedicated theme to keep focus sharp and intentional.</p>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 24px; padding: 14px 16px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($days as $day)
            <div class="admin-card" style="padding: 20px 24px;" id="day-{{ $day->id }}">
                <form method="POST" action="{{ route('admin.scheduler.working-days.update', $day) }}" style="display: flex; flex-wrap: wrap; align-items: center; gap: 16px;">
                    @csrf
                    @method('PUT')

                    <a href="{{ route('admin.scheduler.time-slots', $day) }}" style="display: flex; align-items: center; gap: 12px; width: 170px; flex-shrink: 0; text-decoration: none; transition: opacity 0.15s;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; background-color: {{ $day->color ?? '#d0ad5d' }};"></div>
                        <div>
                            <div style="font-family: 'Space Grotesk', sans-serif; font-weight: 600; color: #1e293b; font-size: 14px;">{{ $day->day_name }}</div>
                            <div style="font-size: 10px; color: #d0ad5d; text-transform: uppercase; letter-spacing: 0.1em;">{{ $day->timeSlots()->count() }} slots →</div>
                        </div>
                    </a>

                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="theme" value="{{ $day->theme }}" class="admin-input" placeholder="Day theme...">
                    </div>

                    <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
                        <input type="color" name="color" value="{{ $day->color ?? '#d0ad5d' }}" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #e5e7eb; cursor: pointer; background: transparent;" title="Theme color">
                        <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ $day->is_active ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #d0ad5d;">
                        </label>
                        <button type="submit" class="admin-btn-outline">Save</button>
                    </div>
                </form>

                @if($day->description)
                    <div style="margin-top: 8px; padding-left: 182px;">
                        <p style="font-size: 12px; color: #94a3b8; margin: 0;">{{ $day->description }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="admin-card" style="margin-top: 32px; padding: 20px;">
        <h3 style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #94a3b8; margin: 0 0 16px 0;">Week at a Glance</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @foreach($days as $day)
                <div style="display: flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; border: 1px solid #e5e7eb; {{ !$day->is_active ? 'opacity: 0.3;' : '' }}">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $day->color ?? '#d0ad5d' }};"></div>
                    <span style="font-size: 11px; color: #64748b; font-weight: 500;">{{ substr($day->day_name, 0, 3) }}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
