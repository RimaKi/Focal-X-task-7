<?php

namespace App\Providers;

use App\Models\Task;
use App\Observers\TaskObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        Task::observe(TaskObserver::class);

        RateLimiter::for('custom_rate_limit', function (Request $request) {
            return ($request->user() && ($request->user()->role()->first()->name =='admin'))
                ? Limit::none() // بدون قيود للمشرفين
                : Limit::perMinute(60); // 60 طلب في الدقيقة للمستخدمين العاديين
        });
    }
}
