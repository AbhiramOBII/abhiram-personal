<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NudgeLog extends Model
{
    protected $fillable = [
        'nudge_type',
        'context_key',
        'shown_date',
        'dismissed_at',
        'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'shown_date' => 'date',
            'dismissed_at' => 'datetime',
            'clicked_at' => 'datetime',
        ];
    }

    public static function alreadyShownToday(string $nudgeType, string $contextKey): bool
    {
        return static::where('nudge_type', $nudgeType)
            ->where('context_key', $contextKey)
            ->where('shown_date', now()->toDateString())
            ->exists();
    }

    public static function logShown(string $nudgeType, string $contextKey): void
    {
        static::firstOrCreate([
            'nudge_type' => $nudgeType,
            'context_key' => $contextKey,
            'shown_date' => now()->toDateString(),
        ]);
    }
}
