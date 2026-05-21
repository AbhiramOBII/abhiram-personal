<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyPlan extends Model
{
    protected $fillable = [
        'plan_date',
        'working_day_id',
        'focus_intention',
        'notes',
        'is_reviewed',
    ];

    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
            'is_reviewed' => 'boolean',
        ];
    }

    public function workingDay(): BelongsTo
    {
        return $this->belongsTo(WorkingDay::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public static function today(): self
    {
        return static::firstOrCreate(
            ['plan_date' => now()->toDateString()],
            ['working_day_id' => WorkingDay::today()?->id]
        );
    }

    public function completionPercentage(): int
    {
        $total = $this->tasks()->where('status', '!=', 'deferred')->count();
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->tasks()->where('status', 'done')->count() / $total) * 100);
    }
}
