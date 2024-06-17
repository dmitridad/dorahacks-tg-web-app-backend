<?php

namespace App\Providers;

use App\Services\TON\SmartContracts\Game;
use App\Services\TON\SmartContracts\GameInterface;
use App\Services\TON\TonCenterHttpGateway;
use App\Services\TON\TonHttpGatewayInterface;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TonHttpGatewayInterface::class, TonCenterHttpGateway::class);
        $this->app->when(TonCenterHttpGateway::class)
            ->needs(HttpMethodsClientInterface::class)
            ->give(function () {
                // TODO check search process
                return new HttpMethodsClient(
                    Psr18ClientDiscovery::find(),
                    Psr17FactoryDiscovery::findRequestFactory(),
                    Psr17FactoryDiscovery::findStreamFactory(),
                );
            });
        $this->app->when(TonCenterHttpGateway::class)
            ->needs('$tonCenterApiKey')
            ->give(env('TON_CENTER_API_KEY'));
        $this->app->when(TonCenterHttpGateway::class)
            ->needs('$isMainNet')
            ->give(env('USE_TON_MAIN_NET'));

        $this->app->bind(GameInterface::class, Game::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
