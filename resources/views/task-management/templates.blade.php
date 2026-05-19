@extends('admin.layouts.app')

@section('title', 'Task Templates')

@php
    $pillarList = ['revenue','operations','marketing','growth','content','creation','product','networking','community','media','brand','podcast','health','recovery','learning'];
@endphp

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    <div style="margin-bottom: 20px;">
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Task Templates</h1>
        <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">Auto-populated into daily plans. Assign to a specific day or make available any day.</p>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Any Day group --}}
    @php $anyDayTemplates = $groupedTemplates->get('any', collect()); @endphp
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
            <span style="font-size: 14px; font-weight: 700; color: #1e293b;">Any Day</span>
            <span style="padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: #f1f5f9; color: #64748b;">{{ $anyDayTemplates->count() }}</span>
        </div>
        <div class="admin-card" style="overflow: hidden; border-radius: 12px;">
            @foreach($anyDayTemplates as $template)
                @include('task-management._template-row', ['template' => $template])
            @endforeach
            @include('task-management._template-form', ['dayId' => ''])
        </div>
    </div>

    {{-- Per-day groups --}}
    @foreach($workingDays as $wd)
        @php $dayTemplates = $groupedTemplates->get($wd->id, collect()); @endphp
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                <span style="font-size: 18px;">{{ $wd->icon_emoji }}</span>
                <span style="font-size: 14px; font-weight: 700; color: #1e293b;">{{ $wd->day_name }}</span>
                <span style="padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: {{ $wd->hex_color }}18; color: {{ $wd->hex_color }}; border: 1px solid {{ $wd->hex_color }}30;">{{ $wd->theme_short ?? $wd->theme }}</span>
                <span style="font-size: 11px; color: #94a3b8;">{{ $dayTemplates->count() }}</span>
            </div>
            <div class="admin-card" style="overflow: hidden; border-radius: 12px;">
                @foreach($dayTemplates as $template)
                    @include('task-management._template-row', ['template' => $template])
                @endforeach
                @include('task-management._template-form', ['dayId' => $wd->id])
            </div>
        </div>
    @endforeach

</div>
@endsection
