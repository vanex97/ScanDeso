<?php

namespace App\Providers;

use App\Helpers\CurrencyHelper;
use App\Services\DesoService;
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
        $this->app->singleton(
            DesoService::class,
            function () {
                return new DesoService();
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $exchangeRates = app(DesoService::class)->getExchangeRate();

        if (!$exchangeRates) {
            return;
        }

        view()->composer('*', function ($view) use ($exchangeRates) {
            $view->with('desoDesoPrice', CurrencyHelper::centsToDollars($exchangeRates['USDCentsPerDeSoExchangeRate']));
        });
    }
}
