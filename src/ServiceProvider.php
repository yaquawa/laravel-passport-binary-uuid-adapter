<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Auth;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Laravel\Passport\Bridge\AccessTokenRepository::class,
            AccessTokenRepository::class
        );

        $this->app->bind(
            \Laravel\Passport\ApiTokenCookieFactory::class,
            ApiTokenCookieFactory::class
        );
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addUserProvider();
    }

    public function addUserProvider()
    {
        Auth::provider('eloquent_with_binary_uuid', function ($app, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new EloquentUserProviderWithBinaryUuid($app['hash'], $config['model']);
        });
    }
}