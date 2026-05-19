<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\TimeBlocksController;
use App\Http\Controllers\Admin\WorkingDaysController;
use App\Http\Controllers\Api\PracticeController as ApiPracticeController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UpskillingController as ApiUpskillingController;
use App\Http\Controllers\Api\AIController as ApiAIController;
use App\Http\Controllers\Api\NudgeController as ApiNudgeController;
use App\Http\Controllers\Api\WeeklyReviewController as ApiWeeklyReviewController;
use App\Http\Controllers\Analytics\AnalyticsController;
use App\Http\Controllers\Dashboard\TodayController;
use App\Http\Controllers\PracticeManagement\PracticeController as PracticeManagementController;
use App\Http\Controllers\TaskManagement\TaskController as TaskManagementController;
use App\Http\Controllers\Upskilling\UpskillingController;
use App\Http\Controllers\WeeklyReview\WeeklyReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// ─── Admin Routes ───
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Today's Dashboard
        Route::get('today', [TodayController::class, 'index'])->name('dashboard.today');

        // Task Management
        Route::get('tasks', [TaskManagementController::class, 'index'])->name('tasks.index');
        Route::get('tasks/templates', [TaskManagementController::class, 'templates'])->name('tasks.templates');
        Route::post('tasks/templates', [TaskManagementController::class, 'storeTemplate'])->name('tasks.templates.store');
        Route::patch('tasks/templates/{template}/toggle', [TaskManagementController::class, 'toggleTemplate'])->name('tasks.templates.toggle');
        Route::delete('tasks/templates/{template}', [TaskManagementController::class, 'destroyTemplate'])->name('tasks.templates.destroy');

        // Practice Management
        Route::get('practices', [PracticeManagementController::class, 'index'])->name('practices.index');
        Route::post('practices', [PracticeManagementController::class, 'store'])->name('practices.store');
        Route::patch('practices/{practice}', [PracticeManagementController::class, 'update'])->name('practices.update');
        Route::delete('practices/{practice}', [PracticeManagementController::class, 'destroy'])->name('practices.destroy');

        // Practice API (JSON)
        Route::post('api/practices/reorder', [ApiPracticeController::class, 'reorder'])->name('api.practices.reorder');
        Route::post('api/practices/{practice}/complete', [ApiPracticeController::class, 'complete'])->name('api.practices.complete');
        Route::post('api/practices/{practice}/uncomplete', [ApiPracticeController::class, 'uncomplete'])->name('api.practices.uncomplete');
        Route::patch('api/practices/logs/{log}/note', [ApiPracticeController::class, 'updateNote'])->name('api.practices.logs.note');

        // Upskilling
        Route::get('upskilling', [UpskillingController::class, 'index'])->name('upskilling.index');
        Route::post('upskilling/domains', [UpskillingController::class, 'storeDomain'])->name('upskilling.domains.store');
        Route::patch('upskilling/domains/{domain}', [UpskillingController::class, 'updateDomain'])->name('upskilling.domains.update');
        Route::post('upskilling/items', [UpskillingController::class, 'storeItem'])->name('upskilling.items.store');
        Route::patch('upskilling/items/{item}', [UpskillingController::class, 'updateItem'])->name('upskilling.items.update');
        Route::post('upskilling/items/{item}/complete', [UpskillingController::class, 'completeItem'])->name('upskilling.items.complete');

        // Upskilling API (JSON)
        Route::post('api/upskilling/sessions/start', [ApiUpskillingController::class, 'startSession'])->name('api.upskilling.sessions.start');
        Route::post('api/upskilling/sessions/{session}/end', [ApiUpskillingController::class, 'endSession'])->name('api.upskilling.sessions.end');
        Route::patch('api/upskilling/sessions/{session}/notes', [ApiUpskillingController::class, 'updateNotes'])->name('api.upskilling.sessions.notes');
        Route::patch('api/upskilling/sessions/{session}/takeaway', [ApiUpskillingController::class, 'updateTakeaway'])->name('api.upskilling.sessions.takeaway');

        // Weekly Review
        Route::get('weekly-review', [WeeklyReviewController::class, 'index'])->name('weekly-review.index');
        Route::get('weekly-review/history', [WeeklyReviewController::class, 'history'])->name('weekly-review.history');

        // Weekly Review API (JSON)
        Route::patch('api/weekly-review/{review}', [ApiWeeklyReviewController::class, 'update'])->name('api.weekly-review.update');
        Route::post('api/weekly-review/{review}/complete', [ApiWeeklyReviewController::class, 'complete'])->name('api.weekly-review.complete');

        // Analytics
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/monthly', [AnalyticsController::class, 'monthly'])->name('analytics.monthly');

        // Analytics API (JSON)
        Route::get('api/analytics/data', [AnalyticsController::class, 'data'])->name('api.analytics.data');

        // Nudges API (JSON)
        Route::get('api/nudges', [ApiNudgeController::class, 'index'])->name('api.nudges.index');
        Route::post('api/nudges/dismiss', [ApiNudgeController::class, 'dismiss'])->name('api.nudges.dismiss');
        Route::post('api/nudges/click', [ApiNudgeController::class, 'click'])->name('api.nudges.click');

        // AI API (JSON)
        Route::post('api/ai/daily-briefing', [ApiAIController::class, 'dailyBriefing'])->name('api.ai.daily-briefing');
        Route::post('api/ai/task-suggestions', [ApiAIController::class, 'taskSuggestions'])->name('api.ai.task-suggestions');
        Route::post('api/ai/weekly-insight/{review}', [ApiAIController::class, 'weeklyInsight'])->name('api.ai.weekly-insight');
        Route::post('api/ai/pattern-insight', [ApiAIController::class, 'patternInsight'])->name('api.ai.pattern-insight');

        // Task API (JSON) — static paths first to avoid wildcard conflicts
        Route::post('api/tasks', [TaskController::class, 'store'])->name('api.tasks.store');
        Route::patch('api/tasks/focus/{plan}', [TaskController::class, 'updateFocus'])->name('api.tasks.focus');
        Route::post('api/tasks/reorder', [TaskController::class, 'reorder'])->name('api.tasks.reorder');
        Route::patch('api/tasks/{task}', [TaskController::class, 'update'])->name('api.tasks.update');
        Route::post('api/tasks/{task}/complete', [TaskController::class, 'complete'])->name('api.tasks.complete');
        Route::post('api/tasks/{task}/defer', [TaskController::class, 'defer'])->name('api.tasks.defer');
        Route::post('api/tasks/{task}/sub-task', [TaskController::class, 'addSubTask'])->name('api.tasks.subtask');
        Route::post('api/tasks/{task}/archive', [TaskController::class, 'archive'])->name('api.tasks.archive');
        Route::patch('api/tasks/{task}/recurring', [TaskController::class, 'setRecurring'])->name('api.tasks.recurring');
        Route::delete('api/tasks/{task}', [TaskController::class, 'destroy'])->name('api.tasks.destroy');

        // Settings — Working Days
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('working-days', [WorkingDaysController::class, 'index'])->name('working-days.index');
            Route::get('working-days/{workingDay}/edit', [WorkingDaysController::class, 'edit'])->name('working-days.edit');
            Route::put('working-days/{workingDay}', [WorkingDaysController::class, 'update'])->name('working-days.update');
            Route::patch('working-days/{workingDay}/toggle', [WorkingDaysController::class, 'toggleActive'])->name('working-days.toggle');

            // Time Blocks / Working Hours
            Route::get('working-hours', [TimeBlocksController::class, 'overview'])->name('working-hours.index');
            Route::get('working-days/{workingDay}/time-blocks', [TimeBlocksController::class, 'index'])->name('time-blocks.index');
            Route::get('time-blocks/{timeBlock}/edit', [TimeBlocksController::class, 'edit'])->name('time-blocks.edit');
            Route::put('time-blocks/{timeBlock}', [TimeBlocksController::class, 'update'])->name('time-blocks.update');
            Route::patch('time-blocks/{timeBlock}/toggle', [TimeBlocksController::class, 'toggleActive'])->name('time-blocks.toggle');
            Route::post('working-days/{workingDay}/time-blocks/reorder', [TimeBlocksController::class, 'reorder'])->name('time-blocks.reorder');
        });
    });
});
