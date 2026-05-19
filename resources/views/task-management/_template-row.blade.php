<div style="padding: 12px 14px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f8fafc; {{ !$template->is_active ? 'opacity: 0.45;' : '' }}">
    {{-- Day badge --}}
    @if($template->workingDay)
        <span style="font-size: 14px;" title="{{ $template->workingDay->day_name }}">{{ $template->workingDay->icon_emoji }}</span>
    @else
        <span style="width: 20px; height: 20px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8;">*</span>
    @endif

    {{-- Time block --}}
    @if($template->timeBlock)
        <span style="padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; background: #f1f5f9; color: #64748b; white-space: nowrap;">{{ $template->timeBlock->name }}</span>
    @endif

    {{-- Title --}}
    <span style="flex: 1; min-width: 0; font-size: 14px; font-weight: 500; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $template->title }}</span>

    {{-- Pillar --}}
    @if($template->pillar)
        <span style="padding: 1px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; background: #f1f5f9; color: #64748b; white-space: nowrap;">{{ $template->pillar }}</span>
    @endif

    {{-- Priority --}}
    <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $template->priority === 'must' ? '#ef4444' : ($template->priority === 'bonus' ? '#22c55e' : '#f59e0b') }};"></span>

    {{-- Toggle active --}}
    <form method="POST" action="{{ route('admin.tasks.templates.toggle', $template) }}" style="display: inline;">
        @csrf
        @method('PATCH')
        <button type="submit" style="width: 32px; height: 18px; border-radius: 9px; border: none; cursor: pointer; position: relative; transition: background 0.2s; background: {{ $template->is_active ? '#22c55e' : '#e5e7eb' }};">
            <span style="position: absolute; top: 2px; {{ $template->is_active ? 'right: 2px;' : 'left: 2px;' }} width: 14px; height: 14px; border-radius: 50%; background: #fff; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.15);"></span>
        </button>
    </form>

    {{-- Delete --}}
    <form method="POST" action="{{ route('admin.tasks.templates.destroy', $template) }}" style="display: inline;" onsubmit="return confirm('Delete this template?')">
        @csrf
        @method('DELETE')
        <button type="submit" style="width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 12px;">✕</button>
    </form>
</div>
