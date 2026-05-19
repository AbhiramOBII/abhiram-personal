<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningItem extends Model
{
    protected $fillable = [
        'skill_domain_id',
        'title',
        'type',
        'source_url',
        'estimated_hours',
        'is_completed',
        'completed_at',
        'priority',
        'notes',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'date',
        ];
    }

    public function skillDomain(): BelongsTo
    {
        return $this->belongsTo(SkillDomain::class);
    }

    public function learningSessions(): HasMany
    {
        return $this->hasMany(LearningSession::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    public function scopeForDomain(Builder $query, int $domainId): Builder
    {
        return $query->where('skill_domain_id', $domainId);
    }

    public function totalHoursLogged(): float
    {
        return round($this->learningSessions()->sum('duration_minutes') / 60, 1);
    }

    public function complete(): void
    {
        $this->is_completed = true;
        $this->completed_at = now()->toDateString();
        $this->save();
    }
}
