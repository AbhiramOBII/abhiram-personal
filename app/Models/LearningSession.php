<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningSession extends Model
{
    protected $fillable = [
        'learning_item_id',
        'skill_domain_id',
        'daily_plan_id',
        'session_date',
        'started_at',
        'ended_at',
        'duration_minutes',
        'takeaway',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function learningItem(): BelongsTo
    {
        return $this->belongsTo(LearningItem::class);
    }

    public function skillDomain(): BelongsTo
    {
        return $this->belongsTo(SkillDomain::class);
    }

    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    public function durationInMinutes(): ?int
    {
        if ($this->duration_minutes) {
            return $this->duration_minutes;
        }

        if ($this->started_at && $this->ended_at) {
            return (int) $this->started_at->diffInMinutes($this->ended_at);
        }

        return null;
    }
}
