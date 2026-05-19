<form method="POST" action="{{ route('admin.tasks.templates.store') }}" style="padding: 12px 14px; display: flex; align-items: center; gap: 8px; background: #fafbfc; flex-wrap: wrap;">
    @csrf
    @if($dayId)
        <input type="hidden" name="working_day_id" value="{{ $dayId }}">
    @endif

    <input type="text" name="title" required placeholder="Template title..." style="flex: 2; min-width: 120px; padding: 8px 10px; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 13px; outline: none; background: #fff;">

    <select name="pillar" style="flex: 1; min-width: 90px; padding: 8px 6px; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 11px; color: #64748b; background: #fff; outline: none;">
        <option value="">Pillar</option>
        @foreach($pillarList as $p)
            <option value="{{ $p }}">{{ ucfirst($p) }}</option>
        @endforeach
    </select>

    <select name="priority" style="width: 80px; padding: 8px 6px; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 11px; color: #64748b; background: #fff; outline: none;">
        <option value="should">Should</option>
        <option value="must">Must</option>
        <option value="bonus">Bonus</option>
    </select>

    <select name="time_block_id" style="flex: 1; min-width: 100px; padding: 8px 6px; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 11px; color: #64748b; background: #fff; outline: none;">
        <option value="">Block</option>
        @foreach($timeBlocks as $block)
            <option value="{{ $block->id }}">{{ $block->name }}</option>
        @endforeach
    </select>

    <input type="number" name="estimated_minutes" placeholder="Min" min="0" max="127" style="width: 56px; padding: 8px 6px; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 11px; color: #64748b; outline: none; background: #fff;">

    <button type="submit" style="padding: 8px 14px; border-radius: 6px; border: none; background: #1e293b; color: #fff; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">+ Add</button>
</form>
