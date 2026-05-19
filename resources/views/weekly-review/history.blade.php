@extends('admin.layouts.app')

@section('title', 'Weekly Review History')

@section('content')
<div class="max-w-[860px] mx-auto pb-32">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Review History</h1>
        <a href="{{ route('admin.weekly-review.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium text-white no-underline transition-all hover:opacity-90" style="background: #4f98a3;">
            Current Week →
        </a>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-8">
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-slate-800 tracking-tight">{{ $avgCompletion }}%</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Avg Completion</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold tracking-tight" style="color: #4f98a3;">{{ $avgIdentityScore }}/10</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Avg Identity Score</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-purple-600 tracking-tight">{{ $totalUpskillHours }}h</p>
            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-semibold">Total Upskill Hours</p>
        </div>
    </div>

    {{-- Review List --}}
    @if($reviews->isEmpty())
        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center">
            <p class="text-sm text-slate-400">No completed reviews yet. Close your first week to see it here.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($reviews as $review)
                <div x-data="{ expanded: false }" class="bg-white border border-slate-200 rounded-2xl overflow-hidden transition-shadow hover:shadow-md">
                    <div class="flex items-center gap-4 px-5 py-4 cursor-pointer" @click="expanded = !expanded">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $review->week_start->format('M j') }} → {{ $review->week_end->format('M j, Y') }}
                            </p>
                            @if($review->next_week_focus)
                                <p class="text-xs text-slate-400 mt-0.5 truncate">{{ $review->next_week_focus }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if($review->identity_score)
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold" style="background: #4f98a315; color: #4f98a3;">{{ $review->identity_score }}/10</span>
                            @endif
                            @if($review->energy_rating)
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-600">⚡ {{ $review->energy_rating }}</span>
                            @endif
                            <span class="text-slate-400 transition-transform" :class="expanded && 'rotate-180'">▼</span>
                        </div>
                    </div>

                    <div x-show="expanded" x-collapse x-cloak class="px-5 pb-5 border-t border-slate-100">
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            @if($review->reflection_win)
                                <div>
                                    <p class="text-[10px] font-semibold text-green-600 uppercase tracking-widest mb-1">Win</p>
                                    <p class="text-xs text-slate-600">{{ $review->reflection_win }}</p>
                                </div>
                            @endif
                            @if($review->reflection_challenge)
                                <div>
                                    <p class="text-[10px] font-semibold text-red-500 uppercase tracking-widest mb-1">Challenge</p>
                                    <p class="text-xs text-slate-600">{{ $review->reflection_challenge }}</p>
                                </div>
                            @endif
                            @if($review->reflection_learning)
                                <div>
                                    <p class="text-[10px] font-semibold text-purple-600 uppercase tracking-widest mb-1">Learning</p>
                                    <p class="text-xs text-slate-600">{{ $review->reflection_learning }}</p>
                                </div>
                            @endif
                            @if($review->reflection_gratitude)
                                <div>
                                    <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-widest mb-1">Gratitude</p>
                                    <p class="text-xs text-slate-600">{{ $review->reflection_gratitude }}</p>
                                </div>
                            @endif
                        </div>
                        @if($review->identity_note)
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Identity Note</p>
                                <p class="text-xs text-slate-600 italic">{{ $review->identity_note }}</p>
                            </div>
                        @endif
                        @if($review->next_week_priorities)
                            <div class="mt-3 pt-3 border-t border-slate-100">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1.5">Priorities Set</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($review->next_week_priorities as $priority)
                                        @if($priority)
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">{{ $priority }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
