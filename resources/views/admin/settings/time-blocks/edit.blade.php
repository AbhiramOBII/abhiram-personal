@extends('admin.layouts.app')

@section('title', 'Edit ' . $timeBlock->name)

@php $hex = $timeBlock->workingDay->hex_color; @endphp

@section('content')
<div style="max-width: 600px; margin: 0 auto;">

    {{-- Header --}}
    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('admin.settings.time-blocks.index', $timeBlock->working_day_id) }}" style="width: 36px; height: 36px; border-radius: 8px; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #64748b; flex-shrink: 0;">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">{{ $timeBlock->name }}</h1>
            <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">{{ $timeBlock->workingDay->icon_emoji }} {{ $timeBlock->workingDay->day_name }} · Edit block</p>
        </div>
    </div>

    @if($errors->any())
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
            @foreach($errors->all() as $error)
                <p style="font-size: 14px; color: #dc2626; margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.time-blocks.update', $timeBlock) }}">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Block Name</label>
            <input type="text" name="name" value="{{ old('name', $timeBlock->name) }}" required class="admin-input" placeholder="e.g. Work Block 1">
        </div>

        {{-- Block Type --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 10px;">Block Type</label>
            @php
                $types = [
                    'work'     => '⚡ Work',
                    'break'    => '☕ Break',
                    'free'     => '🌊 Free',
                    'recovery' => '🌿 Recovery',
                ];
                $currentType = old('block_type', $timeBlock->block_type);
            @endphp
            <div style="display: flex; flex-wrap: wrap; gap: 6px;" id="typePills">
                @foreach($types as $value => $label)
                    <label style="cursor: pointer;">
                        <input type="radio" name="block_type" value="{{ $value }}" {{ $currentType === $value ? 'checked' : '' }} style="display: none;" onchange="updateTypePills()">
                        <span class="type-pill" style="display: inline-block; padding: 8px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid {{ $currentType === $value ? $hex : '#e5e7eb' }}; background: {{ $currentType === $value ? $hex : 'transparent' }}; color: {{ $currentType === $value ? '#fff' : '#64748b' }}; transition: all 0.2s;">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Times --}}
        <div style="display: flex; gap: 12px; margin-bottom: 12px;">
            <div class="admin-card" style="padding: 16px; flex: 1;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Start Time</label>
                <input type="time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($timeBlock->start_time)->format('H:i')) }}" required class="admin-input">
            </div>
            <div class="admin-card" style="padding: 16px; flex: 1;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">End Time</label>
                <input type="time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($timeBlock->end_time)->format('H:i')) }}" required class="admin-input">
            </div>
        </div>

        {{-- Intent --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Intent</label>
            <input type="text" name="intent" value="{{ old('intent', $timeBlock->intent) }}" class="admin-input" placeholder="e.g. Deep focused execution">
        </div>

        {{-- Capacity --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Capacity (max tasks)</label>
            <input type="number" name="capacity" value="{{ old('capacity', $timeBlock->capacity) }}" min="0" max="10" required class="admin-input" style="max-width: 120px;">
        </div>

        {{-- Active Toggle --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 14px; font-weight: 500; color: #1e293b;">Active</span>
                    <p style="font-size: 12px; color: #94a3b8; margin: 2px 0 0;">Include this block in the schedule</p>
                </div>
                <label style="cursor: pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $timeBlock->is_active) ? 'checked' : '' }} id="activeToggleCheckbox" style="display: none;" onchange="updateActiveToggle()">
                    <div id="activeToggle" style="width: 44px; height: 24px; border-radius: 12px; position: relative; transition: background 0.2s; background: {{ $timeBlock->is_active ? $hex : '#e5e7eb' }};">
                        <span id="activeToggleKnob" style="position: absolute; top: 2px; {{ $timeBlock->is_active ? 'right: 2px;' : 'left: 2px;' }} width: 20px; height: 20px; border-radius: 50%; background: #fff; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.15);"></span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Buttons --}}
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.settings.time-blocks.index', $timeBlock->working_day_id) }}" class="admin-btn-outline" style="flex: 1; text-align: center; text-decoration: none; display: block; padding: 14px;">Cancel</a>
            <button type="submit" style="flex: 2; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-size: 15px; font-weight: 600; color: #fff; background: {{ $hex }}; transition: all 0.2s; -webkit-appearance: none;">
                Save Changes
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    var hex = '{{ $hex }}';

    function updateTypePills() {
        document.querySelectorAll('#typePills label').forEach(function(label) {
            var input = label.querySelector('input');
            var span = label.querySelector('.type-pill');
            if (input.checked) {
                span.style.background = hex;
                span.style.color = '#fff';
                span.style.borderColor = hex;
            } else {
                span.style.background = 'transparent';
                span.style.color = '#64748b';
                span.style.borderColor = '#e5e7eb';
            }
        });
    }

    function updateActiveToggle() {
        var cb = document.getElementById('activeToggleCheckbox');
        var toggle = document.getElementById('activeToggle');
        var knob = document.getElementById('activeToggleKnob');
        if (cb.checked) {
            toggle.style.background = hex;
            knob.style.right = '2px';
            knob.style.left = 'auto';
        } else {
            toggle.style.background = '#e5e7eb';
            knob.style.left = '2px';
            knob.style.right = 'auto';
        }
    }
</script>
@endpush
@endsection
