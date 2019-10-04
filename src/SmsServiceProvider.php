<?php

namespace Firmantr3\Sms;

use Firmantr3\Sms\Sms;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{

    /** @return string */
    public function getConfigPath() {
        return __DIR__ . '/../config/sms.php';
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->getConfigPath() => config_path('sms.php'),
        ], 'config');

        $this->app->bind('sms-service', function ($app) {
            return new Sms;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigPath(),
            'sms'
        );
    }
}
