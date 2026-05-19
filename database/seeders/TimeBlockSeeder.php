<?php

namespace Database\Seeders;

use App\Models\TimeBlock;
use App\Models\WorkingDay;
use Illuminate\Database\Seeder;

class TimeBlockSeeder extends Seeder
{
    public function run(): void
    {
        $weekdayBlocks = [
            ['name' => 'Morning Free Zone', 'block_type' => 'free',  'start_time' => '07:00', 'end_time' => '10:30', 'intent' => 'Plan the day, light reading, upskilling',    'capacity' => 2, 'sort_order' => 0],
            ['name' => 'Work Block 1',      'block_type' => 'work',  'start_time' => '10:30', 'end_time' => '14:00', 'intent' => 'Deep focused execution',                      'capacity' => 5, 'sort_order' => 1],
            ['name' => 'Break',             'block_type' => 'break', 'start_time' => '14:00', 'end_time' => '14:30', 'intent' => 'Step away, recharge',                          'capacity' => 0, 'sort_order' => 2],
            ['name' => 'Work Block 2',      'block_type' => 'work',  'start_time' => '14:30', 'end_time' => '18:00', 'intent' => 'Meetings, reviews, follow-ups',                'capacity' => 5, 'sort_order' => 3],
            ['name' => 'Evening Free Zone', 'block_type' => 'free',  'start_time' => '18:00', 'end_time' => '21:00', 'intent' => 'Family, exercise, rest, dinner',               'capacity' => 1, 'sort_order' => 4],
            ['name' => 'Night Session',     'block_type' => 'work',  'start_time' => '21:00', 'end_time' => '23:30', 'intent' => 'Light work, learning, planning tomorrow',      'capacity' => 3, 'sort_order' => 5],
        ];

        $sundayBlocks = [
            ['name' => 'Morning Ease',       'block_type' => 'recovery', 'start_time' => '07:00', 'end_time' => '10:00', 'intent' => 'Wake slowly, journal, stretch',            'capacity' => 1, 'sort_order' => 0],
            ['name' => 'Vision Block',       'block_type' => 'free',     'start_time' => '10:00', 'end_time' => '13:00', 'intent' => 'Weekly review, goal setting, reading',      'capacity' => 2, 'sort_order' => 1],
            ['name' => 'Midday Rest',        'block_type' => 'recovery', 'start_time' => '13:00', 'end_time' => '15:00', 'intent' => 'Nap, walk, unplug',                        'capacity' => 0, 'sort_order' => 2],
            ['name' => 'Reflection Block',   'block_type' => 'free',     'start_time' => '15:00', 'end_time' => '18:00', 'intent' => 'Plan next week, personal projects',        'capacity' => 2, 'sort_order' => 3],
            ['name' => 'Evening Wind Down',  'block_type' => 'recovery', 'start_time' => '18:00', 'end_time' => '21:00', 'intent' => 'Family, light entertainment, dinner',      'capacity' => 0, 'sort_order' => 4],
            ['name' => 'Night Wind Down',    'block_type' => 'recovery', 'start_time' => '21:00', 'end_time' => '23:30', 'intent' => 'Read, sleep prep, gratitude log',          'capacity' => 1, 'sort_order' => 5],
        ];

        // Sunday
        $sundayId = WorkingDay::where('day_number', 0)->value('id');
        foreach ($sundayBlocks as $block) {
            TimeBlock::updateOrCreate(
                ['working_day_id' => $sundayId, 'name' => $block['name']],
                array_merge($block, ['working_day_id' => $sundayId])
            );
        }

        // Monday–Saturday
        for ($d = 1; $d <= 6; $d++) {
            $dayId = WorkingDay::where('day_number', $d)->value('id');
            foreach ($weekdayBlocks as $block) {
                TimeBlock::updateOrCreate(
                    ['working_day_id' => $dayId, 'name' => $block['name']],
                    array_merge($block, ['working_day_id' => $dayId])
                );
            }
        }
    }
}
