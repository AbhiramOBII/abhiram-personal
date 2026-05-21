<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Practice extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'cue',
        'reward',
        'identity_statement',
        'two_minute_version',
        'pillar',
        'hex_color',
        'icon_emoji',
        'icon_path',
        'icon_fallback_emoji',
        'prompt_template',
        'input_type',
        'unit',
        'target_value',
        'frequency_type',
        'frequency_days',
        'stack_after_practice_id',
        'stack_trigger',
        'is_two_minute_enabled',
        'is_active',
        'sort_order',
        'preferred_time',
    ];

    protected $appends = ['icon_url'];

    protected function casts(): array
    {
        return [
            'frequency_days' => 'array',
            'is_two_minute_enabled' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PracticeLog::class);
    }

    public function stackAfter(): BelongsTo
    {
        return $this->belongsTo(Practice::class, 'stack_after_practice_id');
    }

    public function stackedPractices(): HasMany
    {
        return $this->hasMany(Practice::class, 'stack_after_practice_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeReflective(Builder $query): Builder
    {
        return $query->where('type', 'reflective');
    }

    public function scopeBehavioral(Builder $query): Builder
    {
        return $query->where('type', 'behavioral');
    }

    public function isReflective(): bool
    {
        return $this->type === 'reflective';
    }

    public function isBehavioral(): bool
    {
        return $this->type === 'behavioral';
    }

    public function isQuantified(): bool
    {
        return $this->isBehavioral() && !is_null($this->target_value);
    }

    public function getIconUrlAttribute(): string
    {
        if ($this->icon_path) {
            return Storage::url($this->icon_path);
        }
        return '';
    }

    public function getDisplayIconAttribute(): array
    {
        if ($this->icon_path) {
            return ['type' => 'svg', 'value' => $this->icon_url];
        }
        return ['type' => 'emoji', 'value' => $this->icon_fallback_emoji ?? ($this->icon_emoji ?? '&#10024;')];
    }

    public function scopeForToday(Builder $query): Builder
    {
        $dayNumber = now()->dayOfWeek;

        return $query->where(function ($q) use ($dayNumber) {
            $q->where('frequency_type', 'daily')
              ->orWhere(function ($q2) use ($dayNumber) {
                  $q2->where('frequency_type', 'specific_days')
                     ->whereJsonContains('frequency_days', $dayNumber);
              });
        });
    }

    public function currentStreak(): int
    {
        $streak = 0;
        $date = now()->subDay();

        while (true) {
            $log = $this->logs()
                ->where('logged_date', $date->toDateString())
                ->where('is_completed', true)
                ->first();

            if (!$log) {
                break;
            }

            $streak++;
            $date->subDay();
        }

        return $streak;
    }

    public function longestStreak(): int
    {
        $logs = $this->logs()
            ->where('is_completed', true)
            ->orderBy('logged_date')
            ->pluck('logged_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString());

        if ($logs->isEmpty()) {
            return 0;
        }

        $longest = 1;
        $current = 1;

        for ($i = 1; $i < $logs->count(); $i++) {
            $prev = \Carbon\Carbon::parse($logs[$i - 1]);
            $curr = \Carbon\Carbon::parse($logs[$i]);

            if ($prev->diffInDays($curr) === 1) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 1;
            }
        }

        return $longest;
    }

    public function completionRateLastDays(int $days = 30): float
    {
        $since = now()->subDays($days)->toDateString();

        $total = $this->logs()->where('logged_date', '>=', $since)->count();
        $completed = $this->logs()->where('logged_date', '>=', $since)->where('is_completed', true)->count();

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }
}
