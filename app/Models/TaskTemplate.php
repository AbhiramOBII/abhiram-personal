<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTemplate extends Model
{
    protected $fillable = [
        'working_day_id',
        'time_block_id',
        'title',
        'notes',
        'pillar',
        'priority',
        'estimated_minutes',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function workingDay(): BelongsTo
    {
        return $this->belongsTo(WorkingDay::class);
    }

    public function timeBlock(): BelongsTo
    {
        return $this->belongsTo(TimeBlock::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay(Builder $query, int $dayNumber): Builder
    {
        $dayId = WorkingDay::where('day_number', $dayNumber)->value('id');

        return $query->where(function ($q) use ($dayId) {
            $q->where('working_day_id', $dayId)->orWhereNull('working_day_id');
        });
    }
}
