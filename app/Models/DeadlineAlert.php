<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeadlineAlert extends Model
{
    protected $fillable = [
        'task_id',
        'alert_type',
        'ai_message',
        'alert_date',
        'is_dismissed',
    ];

    protected function casts(): array
    {
        return [
            'alert_date' => 'date',
            'is_dismissed' => 'boolean',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
