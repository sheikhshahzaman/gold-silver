<?php

namespace App\Providers;

use App\Services\PriceEngine\PriceCacheManager;
use App\Services\PriceEngine\PriceCalculator;
use App\Services\PriceEngine\PriceFetcher;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PriceCalculator::class, function () {
            return new PriceCalculator();
        });

        $this->app->singleton(PriceCacheManager::class, function () {
            return new PriceCacheManager();
        });

        $this->app->singleton(PriceFetcher::class, function ($app) {
            return new PriceFetcher(
                $app->make(PriceCalculator::class),
                $app->make(PriceCacheManager::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
