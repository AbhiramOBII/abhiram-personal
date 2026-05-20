<?php

namespace Database\Seeders;

use App\Models\Practice;
use Illuminate\Database\Seeder;

class DefaultPracticesSeeder extends Seeder
{
    public function run(): void
    {
        $practices = [
            // ── Reflective Practices ──
            [
                'name' => 'Morning Vision',
                'type' => 'reflective',
                'description' => 'Set your intention and vision for today.',
                'icon_emoji' => '🌅',
                'icon_fallback_emoji' => '🌅',
                'hex_color' => '#f59e0b',
                'prompt_template' => 'Generate a {theme}-day vision prompt for a founder focused on {pillar}. Keep it personal and grounded for a {day}.',
                'input_type' => 'text_long',
                'frequency_type' => 'daily',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Gratitude List',
                'type' => 'reflective',
                'description' => 'List 3 things you are grateful for.',
                'icon_emoji' => '🙏',
                'icon_fallback_emoji' => '🙏',
                'hex_color' => '#10b981',
                'prompt_template' => 'Ask about gratitude on a {theme} day. What specific things from today or this week deserve recognition? Be founder-specific.',
                'input_type' => 'list',
                'frequency_type' => 'daily',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Evening Reflection',
                'type' => 'reflective',
                'description' => 'What did I learn? What would I do differently?',
                'icon_emoji' => '🌙',
                'icon_fallback_emoji' => '🌙',
                'hex_color' => '#6366f1',
                'prompt_template' => 'Generate an end-of-day reflection question for a founder. Today was a {theme} day ({day}). Ask about wins, lessons, or friction. One question only.',
                'input_type' => 'text_long',
                'frequency_type' => 'daily',
                'sort_order' => 3,
                'is_active' => true,
            ],

            // ── Behavioral Practices ──
            ['name' => 'Hydrate',                 'icon_fallback_emoji' => '💧', 'hex_color' => '#006494', 'unit' => 'glasses', 'target_value' => 8,    'sort_order' => 1],
            ['name' => '10 Min Yoga',             'icon_fallback_emoji' => '🧘', 'hex_color' => '#437a22', 'unit' => null,      'target_value' => null,  'sort_order' => 2],
            ['name' => 'Vishnu Sahasranamam',     'icon_fallback_emoji' => '🪔', 'hex_color' => '#d19900', 'unit' => null,      'target_value' => null,  'sort_order' => 3],
            ['name' => '20 Min Screen-Away Time', 'icon_fallback_emoji' => '�️', 'hex_color' => '#964219', 'unit' => 'minutes', 'target_value' => 20,   'sort_order' => 4],
            ['name' => 'Morning Walk',            'icon_fallback_emoji' => '🚶', 'hex_color' => '#01696f', 'unit' => 'minutes', 'target_value' => 20,   'sort_order' => 5],
            ['name' => 'No Sugar',                'icon_fallback_emoji' => '🚫', 'hex_color' => '#a13544', 'unit' => null,      'target_value' => null,  'sort_order' => 6],
        ];

        foreach ($practices as $data) {
            if (!isset($data['type'])) {
                $data['type'] = 'behavioral';
                $data['is_active'] = true;
            }
            Practice::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
