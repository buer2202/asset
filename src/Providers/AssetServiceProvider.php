<?php

namespace Buer\Asset\Providers;

use Illuminate\Support\ServiceProvider;
use Buer\Asset\Commands\DailySettlementPlatformAsset;
use Buer\Asset\Commands\DailySettlementUserAsset;
use Buer\Asset\Asset;

// 资金
class AssetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/asset.php' => config_path('asset.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                DailySettlementPlatformAsset::class,
                DailySettlementUserAsset::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('asset', function ($app) {
            return new Asset;
        });
    }
}
