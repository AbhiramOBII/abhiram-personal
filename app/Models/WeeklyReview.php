<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WeeklyReview extends Model
{
    protected $fillable = [
        'week_start',
        'week_end',
        'reflection_win',
        'reflection_challenge',
        'reflection_learning',
        'reflection_gratitude',
        'next_week_focus',
        'next_week_priorities',
        'identity_score',
        'identity_note',
        'energy_rating',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'next_week_priorities' => 'array',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public static function currentWeek(): self
    {
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $sunday = $monday->copy()->addDays(6);

        return static::firstOrCreate(
            ['week_start' => $monday->toDateString()],
            ['week_end' => $sunday->toDateString()]
        );
    }

    public function complete(): void
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $this->save();
    }
}
