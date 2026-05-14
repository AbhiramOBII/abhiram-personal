<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarTask extends Model
{
    protected $fillable = [
        'calendar_day_id',
        'start_time',
        'end_time',
        'description',
        'pillar',
        'is_completed',
        'sort_order',
        'source_time_slot_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
        ];
    }

    public function calendarDay(): BelongsTo
    {
        return $this->belongsTo(CalendarDay::class);
    }

    public function sourceTimeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'source_time_slot_id');
    }
}
