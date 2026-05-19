<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIOutput extends Model
{
    protected $table = 'ai_outputs';

    protected $fillable = [
        'feature',
        'context_date',
        'content',
        'meta',
        'is_shown',
    ];

    protected function casts(): array
    {
        return [
            'context_date' => 'date',
            'meta' => 'array',
            'is_shown' => 'boolean',
        ];
    }

    public static function getCached(string $feature, string $date): ?self
    {
        return static::where('feature', $feature)
            ->where('context_date', $date)
            ->first();
    }
}
