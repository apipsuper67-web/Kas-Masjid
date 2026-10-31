<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        App::setLocale('id');
        Carbon::setLocale('id');

        RateLimiter::for('login', function (Request $request): Limit {
            $key = Str::transliterate(
                Str::lower($request->string('email')).'|'.$request->ip(),
            );

            return Limit::perMinute(5)->by($key);
        });
    }
}
