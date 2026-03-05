<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\LpbTemporary;
use App\Models\SjTemporary;
use App\Models\ConversionValue;
use App\Observers\LpbTemporaryObserver;
use App\Observers\SjTemporaryObserver;
use App\Observers\ConversionValueObserver;

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
        LpbTemporary::observe(LpbTemporaryObserver::class);
        SjTemporary::observe(SjTemporaryObserver::class);
        ConversionValue::observe(ConversionValueObserver::class);
    }
}
