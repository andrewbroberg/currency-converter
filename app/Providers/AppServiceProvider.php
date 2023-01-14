<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\CurrencyConverter;
use App\Services\CurrencyLayerConverter;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Model::preventAccessingMissingAttributes();
        Model::preventLazyLoading();
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CurrencyConverter::class, function () {
            return new CurrencyLayerConverter(config('services.currency-layer.key'));
        });
    }
}
