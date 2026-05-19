<?php

namespace Database\Seeders;

use App\Models\WorkingDay;
use Illuminate\Database\Seeder;

class WorkingDaySeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            [
                'day_number'     => 0,
                'day_name'       => 'Sunday',
                'theme'          => 'Recovery + Vision Day',
                'theme_short'    => 'Recovery',
                'color'          => 'teal',
                'hex_color'      => '#4f98a3',
                'icon_emoji'     => '🌿',
                'energy_profile' => 'low',
                'pillars'        => ['recovery', 'vision', 'health'],
                'upskill_focus'  => 'Business Strategy & Vision',
                'sort_order'     => 0,
            ],
            [
                'day_number'     => 1,
                'day_name'       => 'Monday',
                'theme'          => 'Revenue & Operations Day',
                'theme_short'    => 'Revenue',
                'color'          => 'gold',
                'hex_color'      => '#e8af34',
                'icon_emoji'     => '💰',
                'energy_profile' => 'high',
                'pillars'        => ['revenue', 'operations', 'client'],
                'upskill_focus'  => 'Business Development & Sales',
                'sort_order'     => 1,
            ],
            [
                'day_number'     => 2,
                'day_name'       => 'Tuesday',
                'theme'          => 'Marketing & Funnel Day',
                'theme_short'    => 'Marketing',
                'color'          => 'orange',
                'hex_color'      => '#fdab43',
                'icon_emoji'     => '📣',
                'energy_profile' => 'medium',
                'pillars'        => ['marketing', 'growth', 'content'],
                'upskill_focus'  => 'Digital Marketing & Growth',
                'sort_order'     => 2,
            ],
            [
                'day_number'     => 3,
                'day_name'       => 'Wednesday',
                'theme'          => 'Deep Creation Day',
                'theme_short'    => 'Creation',
                'color'          => 'purple',
                'hex_color'      => '#a86fdf',
                'icon_emoji'     => '🎨',
                'energy_profile' => 'creative',
                'pillars'        => ['creation', 'product', 'design'],
                'upskill_focus'  => 'Technical Skills & Creative Tools',
                'sort_order'     => 3,
            ],
            [
                'day_number'     => 4,
                'day_name'       => 'Thursday',
                'theme'          => 'BNI + Full Networking Day',
                'theme_short'    => 'Networking',
                'color'          => 'blue',
                'hex_color'      => '#5591c7',
                'icon_emoji'     => '🤝',
                'energy_profile' => 'social',
                'pillars'        => ['networking', 'community', 'relationships'],
                'upskill_focus'  => 'Communication & Sales Skills',
                'sort_order'     => 4,
            ],
            [
                'day_number'     => 5,
                'day_name'       => 'Friday',
                'theme'          => 'Shoot & Media Day',
                'theme_short'    => 'Media',
                'color'          => 'pink',
                'hex_color'      => '#d163a7',
                'icon_emoji'     => '🎬',
                'energy_profile' => 'creative',
                'pillars'        => ['media', 'content', 'brand'],
                'upskill_focus'  => 'Video Production & Editing',
                'sort_order'     => 5,
            ],
            [
                'day_number'     => 6,
                'day_name'       => 'Saturday',
                'theme'          => 'Podcast + Community Day',
                'theme_short'    => 'Podcast',
                'color'          => 'green',
                'hex_color'      => '#6daa45',
                'icon_emoji'     => '🎙️',
                'energy_profile' => 'social',
                'pillars'        => ['podcast', 'community', 'audience'],
                'upskill_focus'  => 'Podcasting & Storytelling',
                'sort_order'     => 6,
            ],
        ];

        foreach ($days as $day) {
            WorkingDay::updateOrCreate(
                ['day_number' => $day['day_number']],
                $day
            );
        }
    }
}
