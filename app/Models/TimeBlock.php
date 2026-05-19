<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeBlock extends Model
{
    protected $fillable = [
        'working_day_id',
        'name',
        'block_type',
        'start_time',
        'end_time',
        'intent',
        'capacity',
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

    public function durationInMinutes(): int
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        return (int) $start->diffInMinutes($end);
    }

    public function blockTypeLabel(): string
    {
        return match ($this->block_type) {
            'work'     => '⚡ Work',
            'break'    => '☕ Break',
            'free'     => '🌊 Free',
            'recovery' => '🌿 Recovery',
            default    => '🌊 Free',
        };
    }

    public static function current(): ?self
    {
        $today = WorkingDay::today();
        if (!$today) {
            return null;
        }

        $now = now()->format('H:i:s');

        return static::where('working_day_id', $today->id)
            ->where('is_active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>', $now)
            ->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
