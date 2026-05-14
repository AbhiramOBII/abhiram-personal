<?php

namespace Database\Seeders;

use App\Models\WorkingDay;
use Illuminate\Database\Seeder;

class WorkingDaysSeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            ['day_number' => 0, 'day_name' => 'Sunday',    'theme' => 'Recovery + Vision Day',        'color' => '#8B5CF6'],
            ['day_number' => 1, 'day_name' => 'Monday',    'theme' => 'Revenue & Operations',         'color' => '#10B981'],
            ['day_number' => 2, 'day_name' => 'Tuesday',   'theme' => 'Marketing & Funnel',           'color' => '#F59E0B'],
            ['day_number' => 3, 'day_name' => 'Wednesday', 'theme' => 'Deep Creation Day',            'color' => '#3B82F6'],
            ['day_number' => 4, 'day_name' => 'Thursday',  'theme' => 'BNI + Full Networking Day',    'color' => '#EC4899'],
            ['day_number' => 5, 'day_name' => 'Friday',    'theme' => 'Shoot & Media Day',            'color' => '#EF4444'],
            ['day_number' => 6, 'day_name' => 'Saturday',  'theme' => 'Podcast + Community Day',      'color' => '#d0ad5d'],
        ];

        foreach ($days as $day) {
            WorkingDay::updateOrCreate(
                ['day_number' => $day['day_number']],
                $day
            );
        }
    }
}
