<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AILog extends Model
{
    public $timestamps = false;

    protected $table = 'ai_logs';

    protected $fillable = [
        'feature',
        'prompt_tokens',
        'completion_tokens',
        'model',
        'prompt_summary',
        'response_summary',
        'latency_ms',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
