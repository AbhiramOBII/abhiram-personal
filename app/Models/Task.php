<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'daily_plan_id',
        'time_block_id',
        'title',
        'notes',
        'pillar',
        'priority',
        'estimated_minutes',
        'is_completed',
        'completed_at',
        'is_rolled_over',
        'rolled_from_date',
        'rollover_count',
        'sort_order',
        'parent_task_id',
        'is_recurring',
        'recurring_days',
        'recurring_type',
        'due_date',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'is_rolled_over' => 'boolean',
            'is_recurring' => 'boolean',
            'completed_at' => 'datetime',
            'rolled_from_date' => 'date',
            'due_date' => 'date',
            'archived_at' => 'datetime',
            'recurring_days' => 'array',
        ];
    }

    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    public function timeBlock(): BelongsTo
    {
        return $this->belongsTo(TimeBlock::class);
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function complete(): void
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $this->save();
    }

    public function defer(): void
    {
        $tomorrow = now()->addDay()->toDateString();
        $tomorrowDay = WorkingDay::where('day_number', now()->addDay()->dayOfWeek)->first();

        $tomorrowPlan = DailyPlan::firstOrCreate(
            ['plan_date' => $tomorrow],
            ['working_day_id' => $tomorrowDay?->id]
        );

        Task::create([
            'daily_plan_id' => $tomorrowPlan->id,
            'time_block_id' => null,
            'title' => $this->title,
            'notes' => $this->notes,
            'pillar' => $this->pillar,
            'priority' => $this->priority,
            'estimated_minutes' => $this->estimated_minutes,
            'is_completed' => false,
            'is_rolled_over' => true,
            'rolled_from_date' => now()->toDateString(),
            'rollover_count' => $this->rollover_count + 1,
            'sort_order' => 0,
        ]);

        $this->delete();
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    public function scopeForBlock(Builder $query, $blockId): Builder
    {
        return $query->where('time_block_id', $blockId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeRecurring(Builder $query): Builder
    {
        return $query->where('is_recurring', true);
    }

    public function archive(): void
    {
        $this->archived_at = now();
        $this->save();
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isBefore(today())
            && !$this->is_completed;
    }
}
