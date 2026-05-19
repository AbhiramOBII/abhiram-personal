<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillDomain extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon_emoji',
        'hex_color',
        'current_level',
        'target_level',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function learningItems(): HasMany
    {
        return $this->hasMany(LearningItem::class);
    }

    public function learningSessions(): HasMany
    {
        return $this->hasMany(LearningSession::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    public function totalHoursLogged(): float
    {
        return round($this->learningSessions()->sum('duration_minutes') / 60, 1);
    }

    public function progressPercentage(): int
    {
        if ($this->target_level == 0) {
            return 0;
        }

        return (int) round(($this->current_level / $this->target_level) * 100);
    }
}
