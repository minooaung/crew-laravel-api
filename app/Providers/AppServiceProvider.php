<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register dev-only service providers in local environment
        if ($this->app->environment('local')) {
            if (class_exists(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class)) {
                $this->app->register(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
            }

            // Example: Laravel Debugbar
            // if (class_exists(\Barryvdh\Debugbar\ServiceProvider::class)) {
            //     $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            // }

            // Example: Laravel Telescope
            // if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            //     $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            // }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
