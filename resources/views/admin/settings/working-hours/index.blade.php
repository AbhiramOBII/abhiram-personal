@extends('admin.layouts.app')

@section('title', 'Working Hours')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Working Hours</h1>
        <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">All time blocks across the week at a glance.</p>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif

    @foreach($days as $day)
        <div class="admin-card" style="margin-bottom: 16px; overflow: hidden;">
            {{-- Day header --}}
            <div style="padding: 14px 16px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; background: {{ $day->hex_color }}08;">
                <span style="font-size: 20px;">{{ $day->icon_emoji }}</span>
                <span style="font-size: 16px; font-weight: 700; color: #1e293b;">{{ $day->day_name }}</span>
                <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: {{ $day->hex_color }}18; color: {{ $day->hex_color }}; border: 1px solid {{ $day->hex_color }}30;">
                    {{ $day->theme_short ?? $day->theme }}
                </span>
                <span style="margin-left: auto; font-size: 11px; color: #94a3b8;">{{ $day->timeBlocks->count() }} blocks</span>
            </div>

            {{-- Time blocks table --}}
            @if($day->timeBlocks->count())
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="padding: 8px 12px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; white-space: nowrap;">Time</th>
                                <th style="padding: 8px 12px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; white-space: nowrap;">Block</th>
                                <th style="padding: 8px 12px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; white-space: nowrap;">Type</th>
                                <th style="padding: 8px 12px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; white-space: nowrap;">Intent</th>
                                <th style="padding: 8px 12px; text-align: center; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; white-space: nowrap;">Cap</th>
                                <th style="padding: 8px 12px; text-align: center; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; white-space: nowrap;">Dur</th>
                                <th style="padding: 8px 12px; text-align: center; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; white-space: nowrap;">Status</th>
                                <th style="padding: 8px 12px; text-align: center; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; white-space: nowrap;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($day->timeBlocks as $block)
                                <tr style="border-top: 1px solid #f1f5f9; {{ !$block->is_active ? 'opacity: 0.45;' : '' }}">
                                    {{-- Time --}}
                                    <td style="padding: 10px 12px; white-space: nowrap;">
                                        <span style="font-family: monospace; font-weight: 600; color: #1e293b;">{{ \Carbon\Carbon::parse($block->start_time)->format('H:i') }}</span>
                                        <span style="color: #cbd5e1; margin: 0 2px;">–</span>
                                        <span style="font-family: monospace; color: #64748b;">{{ \Carbon\Carbon::parse($block->end_time)->format('H:i') }}</span>
                                    </td>

                                    {{-- Name --}}
                                    <td style="padding: 10px 12px; font-weight: 600; color: #1e293b;">{{ $block->name }}</td>

                                    {{-- Type badge --}}
                                    <td style="padding: 10px 12px;">
                                        @php
                                            $typeBg = match($block->block_type) {
                                                'work'     => '#fef3c7',
                                                'break'    => '#fce7f3',
                                                'free'     => '#e0f2fe',
                                                'recovery' => '#ecfdf5',
                                                default    => '#f1f5f9',
                                            };
                                            $typeColor = match($block->block_type) {
                                                'work'     => '#92400e',
                                                'break'    => '#9d174d',
                                                'free'     => '#075985',
                                                'recovery' => '#065f46',
                                                default    => '#64748b',
                                            };
                                        @endphp
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: {{ $typeBg }}; color: {{ $typeColor }}; white-space: nowrap;">
                                            {{ $block->blockTypeLabel() }}
                                        </span>
                                    </td>

                                    {{-- Intent --}}
                                    <td style="padding: 10px 12px; color: #64748b; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $block->intent ?? '—' }}</td>

                                    {{-- Capacity --}}
                                    <td style="padding: 10px 12px; text-align: center; color: #64748b;">{{ $block->capacity }}</td>

                                    {{-- Duration --}}
                                    <td style="padding: 10px 12px; text-align: center; color: #94a3b8; font-size: 12px; white-space: nowrap;">{{ $block->durationInMinutes() }}m</td>

                                    {{-- Active status --}}
                                    <td style="padding: 10px 12px; text-align: center;">
                                        <form method="POST" action="{{ route('admin.settings.time-blocks.toggle', $block) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" style="width: 36px; height: 20px; border-radius: 10px; border: none; cursor: pointer; position: relative; transition: background 0.2s; background: {{ $block->is_active ? $day->hex_color : '#e5e7eb' }};">
                                                <span style="position: absolute; top: 2px; {{ $block->is_active ? 'right: 2px;' : 'left: 2px;' }} width: 16px; height: 16px; border-radius: 50%; background: #fff; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.15);"></span>
                                            </button>
                                        </form>
                                    </td>

                                    {{-- Edit action --}}
                                    <td style="padding: 10px 12px; text-align: center;">
                                        <a href="{{ route('admin.settings.time-blocks.edit', $block) }}" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; color: #94a3b8; text-decoration: none; transition: background 0.15s; border: 1px solid #e5e7eb;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 24px; text-align: center; color: #94a3b8; font-size: 13px;">No time blocks configured.</div>
            @endif
        </div>
    @endforeach

</div>
@endsection
