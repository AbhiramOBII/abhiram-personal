<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkingDay extends Model
{
    protected $fillable = [
        'day_number',
        'day_name',
        'theme',
        'theme_short',
        'color',
        'hex_color',
        'icon_emoji',
        'description',
        'pillars',
        'suggested_task_types',
        'energy_profile',
        'upskill_focus',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'pillars' => 'array',
            'suggested_task_types' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public static function today(): ?self
    {
        return static::where('day_number', now()->dayOfWeek)->first();
    }

    public function energyLabel(): string
    {
        return match ($this->energy_profile) {
            'low'      => '🧘 Low Energy',
            'high'     => '🔥 High Energy',
            'creative' => '🎨 Creative',
            'social'   => '🤝 Social',
            default    => '⚡ Medium',
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('day_number');
    }

    public function timeBlocks(): HasMany
    {
        return $this->hasMany(TimeBlock::class)->orderBy('sort_order')->orderBy('start_time');
    }
}
