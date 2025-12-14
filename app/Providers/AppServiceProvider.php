<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
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
        // Set Filament to RTL for Persian/Farsi support
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn (): string => Blade::render('<meta name="direction" content="rtl">'),
        );

        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => Blade::render('<script>document.documentElement.dir = "rtl"; document.documentElement.lang = "fa";</script>'),
        );
    }
}
