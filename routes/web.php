<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TimeSlotsController;
use App\Http\Controllers\Admin\WorkingDaysController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// ─── Admin Auth Routes ───
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Scheduler
        Route::get('scheduler/working-days', [WorkingDaysController::class, 'index'])->name('scheduler.working-days');
        Route::put('scheduler/working-days/{workingDay}', [WorkingDaysController::class, 'update'])->name('scheduler.working-days.update');

        // Time Slots
        Route::get('scheduler/{workingDay}/time-slots', [TimeSlotsController::class, 'index'])->name('scheduler.time-slots');
        Route::post('scheduler/{workingDay}/time-slots', [TimeSlotsController::class, 'store'])->name('scheduler.time-slots.store');
        Route::put('scheduler/time-slots/{timeSlot}', [TimeSlotsController::class, 'update'])->name('scheduler.time-slots.update');
        Route::delete('scheduler/time-slots/{timeSlot}', [TimeSlotsController::class, 'destroy'])->name('scheduler.time-slots.destroy');

        // Today's Tasks
        Route::get('today', [CalendarController::class, 'today'])->name('today');

        // Calendar
        Route::get('scheduler/calendar', [CalendarController::class, 'index'])->name('scheduler.calendar');
        Route::get('scheduler/calendar/{date}', [CalendarController::class, 'day'])->name('scheduler.calendar.day');
        Route::post('scheduler/calendar/{date}/import', [CalendarController::class, 'importSlots'])->name('scheduler.calendar.import');
        Route::patch('scheduler/calendar/task/{calendarTask}/toggle', [CalendarController::class, 'toggleTask'])->name('scheduler.calendar.toggle-task');
        Route::put('scheduler/calendar/task/{calendarTask}', [CalendarController::class, 'updateTask'])->name('scheduler.calendar.update-task');
        Route::post('scheduler/calendar/{date}/task', [CalendarController::class, 'addTask'])->name('scheduler.calendar.add-task');
        Route::delete('scheduler/calendar/task/{calendarTask}', [CalendarController::class, 'destroyTask'])->name('scheduler.calendar.destroy-task');
        Route::delete('scheduler/calendar/{date}/clear', [CalendarController::class, 'clearDay'])->name('scheduler.calendar.clear');
        Route::patch('scheduler/calendar/{date}/notes', [CalendarController::class, 'updateDayNotes'])->name('scheduler.calendar.notes');
    });
});
