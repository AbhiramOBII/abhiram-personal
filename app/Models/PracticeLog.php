<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeLog extends Model
{
    protected $fillable = [
        'practice_id',
        'daily_plan_id',
        'logged_date',
        'is_completed',
        'used_two_minute_version',
        'completed_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'used_two_minute_version' => 'boolean',
            'logged_date' => 'date',
            'completed_at' => 'datetime',
            'note' => 'string',
        ];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    public function scopeForDate(Builder $query, $date): Builder
    {
        return $query->where('logged_date', $date);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }
}
