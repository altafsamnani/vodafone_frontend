<?php

namespace App\Providers;

use App\Services\Contract\OauthServiceInterface;
use App\Services\OAuth\Keycloak\KeycloakService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OauthServiceInterface::class, function ($app) {
            $service = match($app->request->route()->parameter('provider')) {
                            'keycloak' => KeycloakService::class,
                            default => throw new \Exception('Incorrect provider')
                        };

            return $app->make($service);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
