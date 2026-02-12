<?php

namespace App\Providers;

use App\Contracts\FlasherInterface;
use App\Contracts\TransactionManagerInterface;
use App\Services\Database\TransactionManager;
use App\Services\Notification\Flasher;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;

class AppServiceProvider extends ServiceProvider
{
    use LoadsTranslatedCachedRoutes;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // transaction manager
        $this->app->bind(TransactionManagerInterface::class, TransactionManager::class);

        // flasher
        $this->app->bind(FlasherInterface::class, Flasher::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {

            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);

            $this->app->register(TelescopeServiceProvider::class);
        }

        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        RouteServiceProvider::loadCachedRoutesUsing(fn() => $this->loadCachedRoutes());
    }
}
