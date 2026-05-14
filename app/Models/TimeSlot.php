<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    protected $fillable = [
        'working_day_id',
        'start_time',
        'end_time',
        'description',
        'pillar',
        'sort_order',
    ];

    public function workingDay(): BelongsTo
    {
        return $this->belongsTo(WorkingDay::class);
    }
}
