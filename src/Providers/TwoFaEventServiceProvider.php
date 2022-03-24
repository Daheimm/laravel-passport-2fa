<?php

namespace TwoFA\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Passport\Token;
use TwoFA\Observers\TokenObserver;

class TwoFaEventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        Token::observe(TokenObserver::class);
    }
}
