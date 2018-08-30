<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

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
            Bridge\AccessTokenRepository::class
        );

        $this->app->bind(
            \Laravel\Passport\Bridge\UserRepository::class,
            Bridge\UserRepository::class
        );

        $this->app->bind(
            \Laravel\Passport\Bridge\AuthCodeRepository::class,
            Bridge\AuthCodeRepository::class
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
        $this->switchModels();
        $this->addUserProvider();
    }

    protected function switchModels(): void
    {
        Passport::useTokenModel(Token::class);
        Passport::useClientModel(Client::class);
        Passport::useAuthCodeModel(AuthCode::class);
    }

    protected function addUserProvider()
    {
        Auth::provider('eloquent_with_binary_uuid', function ($app, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new EloquentUserProviderWithBinaryUuid($app['hash'], $config['model']);
        });
    }
}