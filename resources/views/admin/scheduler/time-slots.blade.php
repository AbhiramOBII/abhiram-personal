@extends('admin.layouts.app')

@section('title', $workingDay->day_name . ' — Time Slots')

@section('content')
<div style="max-width: 960px;">

    <div style="margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #d0ad5d;">Scheduler</span>
            <span style="color: #cbd5e1;">→</span>
            <a href="{{ route('admin.scheduler.working-days') }}" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #94a3b8; text-decoration: none;">Working Days</a>
            <span style="color: #cbd5e1;">→</span>
            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #64748b;">{{ $workingDay->day_name }}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0; background-color: {{ $workingDay->color ?? '#d0ad5d' }};"></div>
            <div>
                <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">{{ $workingDay->day_name }}</h1>
                <p style="margin-top: 4px; font-size: 14px; color: #94a3b8;">{{ $workingDay->theme }}</p>
            </div>
        </div>
    </div>

    <!-- Day Tabs -->
    <div style="display: flex; gap: 8px; margin-bottom: 32px; overflow-x: auto; padding-bottom: 8px;">
        @foreach($days as $d)
            <a href="{{ route('admin.scheduler.time-slots', $d) }}"
               style="flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.15s; {{ $d->id === $workingDay->id ? 'background: #fef8ec; color: #92700c; border: 1px solid #fde68a;' : 'color: #94a3b8; border: 1px solid #e5e7eb;' }}">
                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $d->color ?? '#d0ad5d' }};"></div>
                {{ substr($d->day_name, 0, 3) }}
            </a>
        @endforeach
    </div>

    @if(session('success'))
        <div style="margin-bottom: 24px; padding: 14px 16px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div style="margin-bottom: 24px; padding: 14px 16px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
            @foreach($errors->all() as $error)
                <p style="font-size: 14px; color: #dc2626; margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if($workingDay->timeSlots->count())
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 40px;">
            @foreach($workingDay->timeSlots as $index => $slot)
                <div class="admin-card" style="padding: 16px 20px;">
                    <form method="POST" action="{{ route('admin.scheduler.time-slots.update', $slot) }}" style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
                        @csrf
                        @method('PUT')
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span style="font-size: 11px; font-weight: 700; color: #94a3b8;">{{ $index + 1 }}</span>
                        </div>
                        <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}" class="admin-input" style="width: 110px; flex-shrink: 0;">
                        <span style="color: #cbd5e1; font-size: 12px;">to</span>
                        <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}" class="admin-input" style="width: 110px; flex-shrink: 0;">
                        <input type="text" name="description" value="{{ $slot->description }}" class="admin-input" style="flex: 1; min-width: 180px;">
                        <input type="text" name="pillar" value="{{ $slot->pillar }}" class="admin-input" style="width: 150px; flex-shrink: 0;">
                        <button type="submit" class="admin-btn-outline">Save</button>
                    </form>
                    <div style="display: flex; justify-content: flex-end; margin-top: 6px;">
                        <form method="POST" action="{{ route('admin.scheduler.time-slots.destroy', $slot) }}" onsubmit="return confirm('Remove this time slot?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="font-size: 11px; color: #cbd5e1; background: none; border: none; cursor: pointer; transition: color 0.15s;">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="admin-card" style="padding: 40px; text-align: center; margin-bottom: 40px;">
            <svg style="width: 24px; height: 24px; color: #cbd5e1; margin: 0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">No time slots yet for {{ $workingDay->day_name }}.</p>
            <p style="font-size: 12px; color: #cbd5e1; margin-top: 4px;">Add your first block below.</p>
        </div>
    @endif

    <div class="admin-card" style="padding: 24px;">
        <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
            <svg style="width: 16px; height: 16px; color: #d0ad5d;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Time Slot
        </h3>
        <form method="POST" action="{{ route('admin.scheduler.time-slots.store', $workingDay) }}" style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px;">
            @csrf
            <div>
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Start</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}" required class="admin-input" style="width: 130px;">
            </div>
            <div>
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">End</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}" required class="admin-input" style="width: 130px;">
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Description</label>
                <input type="text" name="description" value="{{ old('description') }}" required placeholder="e.g. Team standup..." class="admin-input">
            </div>
            <div style="width: 150px;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Pillar</label>
                <input type="text" name="pillar" value="{{ old('pillar') }}" required placeholder="e.g. Revenue" class="admin-input">
            </div>
            <button type="submit" class="admin-btn-gold">Add</button>
        </form>
    </div>

    @if($workingDay->timeSlots->count())
        <div class="admin-card" style="margin-top: 32px; padding: 20px;">
            <h3 style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2em; color: #94a3b8; margin: 0 0 10px 0;">Day Summary</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 16px; font-size: 14px;">
                <div><span style="color: #94a3b8;">Slots:</span> <strong style="color: #1e293b;">{{ $workingDay->timeSlots->count() }}</strong></div>
                <div><span style="color: #94a3b8;">Pillars:</span> <strong style="color: #1e293b;">{{ $workingDay->timeSlots->pluck('pillar')->unique()->implode(', ') }}</strong></div>
            </div>
        </div>
    @endif

</div>
@endsection
