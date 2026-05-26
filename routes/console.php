<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('dayos:check-deadlines')->dailyAt('09:00');
Schedule::command('dayos:recalculate-scores')->dailyAt('00:01');
Schedule::command('dayos:recalculate-scores')->dailyAt('09:00');
Schedule::command('dayos:resurface-tasks')->dailyAt('09:05');
