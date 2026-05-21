@php $sc = $task->statusConfig; @endphp
<div class="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-slate-200 transition-all hover:shadow-sm"
     style="{{ $task->status === 'wip' ? 'border-left: 3px solid #006494;' : '' }} {{ $task->status === 'done' ? 'opacity: 0.45;' : '' }}">

    {{-- Status badge --}}
    <span class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5 text-[13px]"
          style="background: {{ $sc['bg'] }}; color: {{ $sc['color'] }};">{{ $sc['emoji'] }}</span>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[13px] font-semibold text-slate-800 truncate" style="{{ $task->status === 'done' ? 'text-decoration: line-through; color: #94a3b8;' : '' }}">{{ $task->title }}</span>
            @if($task->pillar)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200 whitespace-nowrap">{{ ucfirst($task->pillar) }}</span>
            @endif
            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap" style="background: {{ $task->priority === 'must' ? '#fef2f2' : ($task->priority === 'bonus' ? '#f0fdf4' : '#fffbeb') }}; color: {{ $task->priority === 'must' ? '#dc2626' : ($task->priority === 'bonus' ? '#16a34a' : '#d97706') }};">{{ ucfirst($task->priority) }}</span>
        </div>

        {{-- Deadline info --}}
        @if($task->deadline_at)
        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
            @if($task->deadline_badge)
            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                  style="background: {{ $task->deadline_badge['bg'] }}; color: {{ $task->deadline_badge['color'] }};">
                {{ $task->deadline_badge['icon'] }} {{ $task->deadline_badge['label'] }}
            </span>
            @endif
            <span class="text-[11px] text-slate-400">{{ $task->deadline_formatted }}</span>
            @if($task->deadline_notes)
                <span class="text-[11px] text-slate-300">&middot; {{ $task->deadline_notes }}</span>
            @endif
        </div>
        @endif

        {{-- Progress bar — days elapsed vs total --}}
        @if($task->start_date && $task->deadline_at)
        @php
            $total   = max(1, $task->start_date->diffInDays($task->deadline_at));
            $elapsed = min($total, $task->start_date->diffInDays(today()));
            $pct     = round(($elapsed / $total) * 100);
            $barColor = $pct >= 90 ? '#a13544' : ($pct >= 70 ? '#da7101' : '#437a22');
        @endphp
        <div class="mt-2 rounded-full h-1 overflow-hidden" style="background: #f1f5f9;">
            <div style="width: {{ $pct }}%; height: 100%; background: {{ $barColor }}; border-radius: 999px; transition: width 0.3s;"></div>
        </div>
        <span class="text-[10px] text-slate-400 mt-0.5 inline-block">Day {{ $elapsed }} of {{ $total }}</span>
        @endif
    </div>
</div>
