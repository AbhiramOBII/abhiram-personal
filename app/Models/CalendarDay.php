<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarDay extends Model
{
    protected $fillable = [
        'date',
        'notes',
        'is_completed',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_completed' => 'boolean',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CalendarTask::class)->orderBy('sort_order')->orderBy('start_time');
    }

    public function completedTasks(): HasMany
    {
        return $this->tasks()->where('is_completed', true);
    }

    public function completionPercent(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        return (int) round(($this->completedTasks()->count() / $total) * 100);
    }
}
