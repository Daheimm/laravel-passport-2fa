<?php

namespace TwoFA\Providers;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\ServiceProvider;
use PragmaRX\Google2FA\Google2FA;
use TwoFA\Contracts\TwoFaAuthInterface;
use TwoFA\Service\TwoFaAuth;


class TwoFaAppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TwoFaAuthInterface::class, function ($app) {
            return new TwoFaAuth(
                $app->make(Google2FA::class),
                $app->make(Repository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
