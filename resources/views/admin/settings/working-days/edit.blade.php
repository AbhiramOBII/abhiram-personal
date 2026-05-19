@extends('admin.layouts.app')

@section('title', 'Edit ' . $workingDay->day_name)

@section('content')
<div style="max-width: 600px; margin: 0 auto;">

    {{-- Header --}}
    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('admin.settings.working-days.index') }}" style="width: 36px; height: 36px; border-radius: 8px; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #64748b; flex-shrink: 0;">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">
                <span id="previewEmoji">{{ $workingDay->icon_emoji }}</span> {{ $workingDay->day_name }}
            </h1>
            <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">Day {{ $workingDay->day_number }} · Edit theme and settings</p>
        </div>
    </div>

    @if($errors->any())
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
            @foreach($errors->all() as $error)
                <p style="font-size: 14px; color: #dc2626; margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.working-days.update', $workingDay) }}">
        @csrf
        @method('PUT')

        {{-- Theme --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Theme</label>
            <input type="text" name="theme" value="{{ old('theme', $workingDay->theme) }}" required class="admin-input" placeholder="e.g. Revenue & Operations Day">
        </div>

        {{-- Theme Short --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Short Label</label>
            <input type="text" name="theme_short" value="{{ old('theme_short', $workingDay->theme_short) }}" class="admin-input" placeholder="e.g. Revenue">
        </div>

        {{-- Color + Emoji row --}}
        <div style="display: flex; gap: 12px; margin-bottom: 12px;">
            <div class="admin-card" style="padding: 16px; flex: 1;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Color</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="color" name="hex_color" id="hexColorInput" value="{{ old('hex_color', $workingDay->hex_color) }}" style="width: 44px; height: 44px; border: 2px solid #e5e7eb; border-radius: 10px; padding: 2px; cursor: pointer; background: transparent;" onchange="updateColorPreview(this.value)">
                    <div>
                        <span id="hexPreview" style="font-family: monospace; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $workingDay->hex_color }}</span>
                        <div id="colorSwatchPreview" style="margin-top: 4px; width: 60px; height: 6px; border-radius: 3px; background: {{ $workingDay->hex_color }};"></div>
                    </div>
                </div>
            </div>
            <div class="admin-card" style="padding: 16px; width: 120px; flex-shrink: 0;">
                <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Emoji</label>
                <input type="text" name="icon_emoji" id="emojiInput" value="{{ old('icon_emoji', $workingDay->icon_emoji) }}" required class="admin-input" style="text-align: center; font-size: 24px; padding: 6px;" oninput="document.getElementById('previewEmoji').textContent=this.value">
            </div>
        </div>

        {{-- Description --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Description</label>
            <textarea name="description" rows="2" class="admin-input" style="resize: none; padding: 10px 14px;" placeholder="Optional day description...">{{ old('description', $workingDay->description) }}</textarea>
        </div>

        {{-- Energy Profile --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 10px;">Energy Profile</label>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;" id="energyPills">
                @php
                    $energyOptions = [
                        'low'      => '🧘 Low',
                        'medium'   => '⚡ Medium',
                        'high'     => '🔥 High',
                        'creative' => '🎨 Creative',
                        'social'   => '🤝 Social',
                    ];
                    $currentEnergy = old('energy_profile', $workingDay->energy_profile);
                    $hexColor = old('hex_color', $workingDay->hex_color);
                @endphp
                @foreach($energyOptions as $value => $label)
                    <label style="cursor: pointer;">
                        <input type="radio" name="energy_profile" value="{{ $value }}" {{ $currentEnergy === $value ? 'checked' : '' }} style="display: none;" onchange="updateEnergyPills()">
                        <span class="energy-pill" data-value="{{ $value }}" style="display: inline-block; padding: 8px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid {{ $currentEnergy === $value ? $hexColor : '#e5e7eb' }}; background: {{ $currentEnergy === $value ? $hexColor : 'transparent' }}; color: {{ $currentEnergy === $value ? '#fff' : '#64748b' }}; transition: all 0.2s;">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Pillars --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 10px;">Pillars</label>
            @php
                $allPillars = ['recovery','vision','health','revenue','operations','client','marketing','growth','content','creation','product','design','networking','community','relationships','media','brand','podcast','audience'];
                $currentPillars = old('pillars', $workingDay->pillars ?? []);
            @endphp
            <div style="display: flex; flex-wrap: wrap; gap: 6px;" id="pillarChips">
                @foreach($allPillars as $pillar)
                    <label style="cursor: pointer;">
                        <input type="checkbox" name="pillars[]" value="{{ $pillar }}" {{ in_array($pillar, $currentPillars) ? 'checked' : '' }} style="display: none;" onchange="updatePillarChips()">
                        <span class="pillar-chip" style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; border: 1px solid {{ in_array($pillar, $currentPillars) ? $hexColor : '#e5e7eb' }}; background: {{ in_array($pillar, $currentPillars) ? $hexColor . '18' : 'transparent' }}; color: {{ in_array($pillar, $currentPillars) ? $hexColor : '#94a3b8' }}; transition: all 0.15s;">
                            {{ $pillar }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Upskill Focus --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 12px;">
            <label style="display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 6px;">Upskill Focus</label>
            <input type="text" name="upskill_focus" value="{{ old('upskill_focus', $workingDay->upskill_focus) }}" class="admin-input" placeholder="e.g. Business Development & Sales">
        </div>

        {{-- Active Toggle --}}
        <div class="admin-card" style="padding: 16px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 14px; font-weight: 500; color: #1e293b;">Active</span>
                    <p style="font-size: 12px; color: #94a3b8; margin: 2px 0 0;">Include this day in your schedule</p>
                </div>
                <label style="cursor: pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $workingDay->is_active) ? 'checked' : '' }} id="activeToggleCheckbox" style="display: none;" onchange="updateActiveToggle()">
                    <div id="activeToggle" style="width: 44px; height: 24px; border-radius: 12px; position: relative; transition: background 0.2s; background: {{ $workingDay->is_active ? $hexColor : '#e5e7eb' }};">
                        <span id="activeToggleKnob" style="position: absolute; top: 2px; {{ $workingDay->is_active ? 'right: 2px;' : 'left: 2px;' }} width: 20px; height: 20px; border-radius: 50%; background: #fff; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.15);"></span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Save --}}
        <button type="submit" id="saveBtn" style="width: 100%; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-size: 15px; font-weight: 600; color: #fff; background: {{ $hexColor }}; transition: all 0.2s; -webkit-appearance: none;">
            Save Changes
        </button>
    </form>

</div>

@push('scripts')
<script>
    function getHexColor() {
        return document.getElementById('hexColorInput').value;
    }

    function updateColorPreview(hex) {
        document.getElementById('hexPreview').textContent = hex;
        document.getElementById('colorSwatchPreview').style.background = hex;
        document.getElementById('saveBtn').style.background = hex;
        updateEnergyPills();
        updatePillarChips();
        updateActiveToggle();
    }

    function updateEnergyPills() {
        var hex = getHexColor();
        document.querySelectorAll('#energyPills label').forEach(function(label) {
            var input = label.querySelector('input');
            var span = label.querySelector('.energy-pill');
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

    function updatePillarChips() {
        var hex = getHexColor();
        document.querySelectorAll('#pillarChips label').forEach(function(label) {
            var input = label.querySelector('input');
            var span = label.querySelector('.pillar-chip');
            if (input.checked) {
                span.style.background = hex + '18';
                span.style.color = hex;
                span.style.borderColor = hex;
            } else {
                span.style.background = 'transparent';
                span.style.color = '#94a3b8';
                span.style.borderColor = '#e5e7eb';
            }
        });
    }

    function updateActiveToggle() {
        var hex = getHexColor();
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
