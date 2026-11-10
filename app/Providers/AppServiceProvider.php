<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
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
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        Paginator::useBootstrapFive();

        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! $this->app->isProduction());

        RateLimiter::for('login', function (Request $request) {
            $username = strtolower(trim((string) $request->input('username')));
            return Limit::perMinute(5)->by($username . '|' . $request->ip());
        });
    }
}
