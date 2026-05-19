<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nudge extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'cta_label',
        'cta_url',
        'icon_emoji',
        'hex_color',
        'priority',
        'is_dismissible',
        'auto_dismiss_seconds',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_dismissible' => 'boolean',
            'auto_dismiss_seconds' => 'integer',
        ];
    }
}
