@extends('admin.layouts.app')

@section('title', 'Working Days')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Working Days</h1>
        <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">Configure your weekly rhythm — themes, energy, and pillars.</p>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 10px;">
        @foreach($days as $day)
            <div class="admin-card" style="border-left: 4px solid {{ $day->hex_color }}; {{ !$day->is_active ? 'opacity: 0.45;' : '' }} transition: opacity 0.2s;">
                <div style="padding: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">

                        {{-- Emoji --}}
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $day->hex_color }}12; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 22px;">
                            {{ $day->icon_emoji }}
                        </div>

                        {{-- Content --}}
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="font-size: 16px; font-weight: 600; color: #1e293b;">{{ $day->day_name }}</span>
                                <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: {{ $day->hex_color }}18; color: {{ $day->hex_color }}; border: 1px solid {{ $day->hex_color }}30;">
                                    {{ $day->theme_short ?? $day->theme }}
                                </span>
                            </div>

                            <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">{{ $day->theme }}</p>

                            {{-- Energy label --}}
                            <div style="margin-top: 8px; display: flex; align-items: center; gap: 6px;">
                                <span style="font-size: 11px; color: #94a3b8;">{{ $day->energyLabel() }}</span>
                            </div>

                            {{-- Pillar chips --}}
                            @if($day->pillars)
                                <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 4px;">
                                    @foreach($day->pillars as $pillar)
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 500; background: #f1f5f9; color: #64748b; border: 1px solid #e5e7eb;">
                                            {{ $pillar }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Upskill --}}
                            @if($day->upskill_focus)
                                <div style="margin-top: 6px; font-size: 11px; color: #94a3b8;">
                                    📚 {{ $day->upskill_focus }}
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0;">
                            {{-- Toggle --}}
                            <form method="POST" action="{{ route('admin.settings.working-days.toggle', $day) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="width: 44px; height: 24px; border-radius: 12px; border: none; cursor: pointer; position: relative; transition: background 0.2s; background: {{ $day->is_active ? $day->hex_color : '#e5e7eb' }};">
                                    <span style="position: absolute; top: 2px; {{ $day->is_active ? 'right: 2px;' : 'left: 2px;' }} width: 20px; height: 20px; border-radius: 50%; background: #fff; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.15);"></span>
                                </button>
                            </form>

                            {{-- Time Blocks --}}
                            <a href="{{ route('admin.settings.time-blocks.index', $day) }}" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #94a3b8; text-decoration: none; transition: background 0.15s;" title="Time Blocks">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('admin.settings.working-days.edit', $day) }}" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #94a3b8; text-decoration: none; transition: background 0.15s;" title="Edit Day">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
