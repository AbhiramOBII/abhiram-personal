@extends('admin.layouts.app')

@section('title', $workingDay->day_name . ' — Time Blocks')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">

    {{-- Header --}}
    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('admin.settings.working-days.index') }}" style="width: 36px; height: 36px; border-radius: 8px; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #64748b; flex-shrink: 0;">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span style="font-size: 22px;">{{ $workingDay->icon_emoji }}</span>
                <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">{{ $workingDay->day_name }}</h1>
                <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; background: {{ $workingDay->hex_color }}18; color: {{ $workingDay->hex_color }}; border: 1px solid {{ $workingDay->hex_color }}30;">
                    {{ $workingDay->theme_short ?? $workingDay->theme }}
                </span>
            </div>
            <p style="margin-top: 2px; font-size: 13px; color: #94a3b8;">{{ $blocks->count() }} time blocks configured</p>
        </div>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p style="font-size: 14px; color: #16a34a; margin: 0;">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Block list --}}
    <div style="display: flex; flex-direction: column; gap: 8px;" id="blockList">
        @foreach($blocks as $block)
            <div class="admin-card" data-id="{{ $block->id }}" style="{{ !$block->is_active ? 'opacity: 0.45;' : '' }} transition: opacity 0.2s;">
                <div style="padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px;">

                    {{-- Drag handle --}}
                    <div class="drag-handle" style="cursor: grab; padding: 4px 0; flex-shrink: 0; color: #cbd5e1; touch-action: none;">
                        <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
                    </div>

                    {{-- Time --}}
                    <div style="width: 100px; flex-shrink: 0;">
                        <span style="font-family: monospace; font-size: 13px; font-weight: 600; color: #1e293b;">
                            {{ \Carbon\Carbon::parse($block->start_time)->format('H:i') }}
                        </span>
                        <span style="font-size: 11px; color: #cbd5e1;">–</span>
                        <span style="font-family: monospace; font-size: 13px; font-weight: 500; color: #64748b;">
                            {{ \Carbon\Carbon::parse($block->end_time)->format('H:i') }}
                        </span>
                        <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">{{ $block->durationInMinutes() }} min</div>
                    </div>

                    {{-- Content --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span style="font-size: 14px; font-weight: 600; color: #1e293b;">{{ $block->name }}</span>
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
                            <span style="display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; background: {{ $typeBg }}; color: {{ $typeColor }};">
                                {{ $block->blockTypeLabel() }}
                            </span>
                        </div>
                        @if($block->intent)
                            <p style="font-size: 12px; color: #94a3b8; margin: 4px 0 0;">{{ $block->intent }}</p>
                        @endif
                        @if($block->capacity > 0)
                            <span style="font-size: 10px; color: #cbd5e1; margin-top: 4px; display: inline-block;">up to {{ $block->capacity }} tasks</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0;">
                        <form method="POST" action="{{ route('admin.settings.time-blocks.toggle', $block) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="width: 40px; height: 22px; border-radius: 11px; border: none; cursor: pointer; position: relative; transition: background 0.2s; background: {{ $block->is_active ? $workingDay->hex_color : '#e5e7eb' }};">
                                <span style="position: absolute; top: 2px; {{ $block->is_active ? 'right: 2px;' : 'left: 2px;' }} width: 18px; height: 18px; border-radius: 50%; background: #fff; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.15);"></span>
                            </button>
                        </form>
                        <a href="{{ route('admin.settings.time-blocks.edit', $block) }}" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; color: #94a3b8; text-decoration: none;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

@push('scripts')
<script>
(function() {
    var list = document.getElementById('blockList');
    var dragged = null;

    list.addEventListener('mousedown', startDrag);
    list.addEventListener('touchstart', startDrag, {passive: false});

    function startDrag(e) {
        var handle = e.target.closest('.drag-handle');
        if (!handle) return;
        e.preventDefault();
        dragged = handle.closest('[data-id]');
        dragged.style.opacity = '0.5';
        document.addEventListener('mouseup', endDrag);
        document.addEventListener('touchend', endDrag);
        document.addEventListener('mousemove', onDrag);
        document.addEventListener('touchmove', onDrag, {passive: false});
    }

    function onDrag(e) {
        if (!dragged) return;
        e.preventDefault();
        var y = e.touches ? e.touches[0].clientY : e.clientY;
        var cards = Array.from(list.querySelectorAll('[data-id]'));
        for (var i = 0; i < cards.length; i++) {
            var rect = cards[i].getBoundingClientRect();
            if (y < rect.top + rect.height / 2) {
                list.insertBefore(dragged, cards[i]);
                return;
            }
        }
        list.appendChild(dragged);
    }

    function endDrag() {
        if (!dragged) return;
        dragged.style.opacity = '';
        dragged = null;
        document.removeEventListener('mouseup', endDrag);
        document.removeEventListener('touchend', endDrag);
        document.removeEventListener('mousemove', onDrag);
        document.removeEventListener('touchmove', onDrag);
        saveOrder();
    }

    function saveOrder() {
        var ids = Array.from(list.querySelectorAll('[data-id]')).map(function(el) { return parseInt(el.dataset.id); });
        fetch("{{ route('admin.settings.time-blocks.reorder', $workingDay) }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            body: JSON.stringify({ ids: ids })
        });
    }
})();
</script>
@endpush
@endsection
