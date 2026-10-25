<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Set standar lokal Indonesia & Mata Uang Rupiah (IDR) untuk seluruh aplikasi & Filament
        Number::useLocale('id');
        Number::useCurrency('IDR');

        // Rate Limiting API (/api/v1/users - Sesuai Swimlane Flowchart & UTS T08)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
