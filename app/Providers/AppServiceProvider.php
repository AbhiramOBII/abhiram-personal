<?php

namespace App\Providers;

use App\Models\WorkingDay;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.layouts.app', function ($view) {
            $today = WorkingDay::today();
            $view->with('todayHexColor', $today?->hex_color ?? '#64748b');
        });
    }
}
