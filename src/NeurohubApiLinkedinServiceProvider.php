<?php

namespace Neurohub\Apilinkedin;

use Illuminate\Support\ServiceProvider;

class NeurohubApiLinkedinServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'share_post');
        $this->publishes([
            __DIR__ . '/config/apiservice.php' => config_path('apiservice.php'),
        ]);
        $this->publishes([
            __DIR__ . '/config/linkedinsharecontent.php' => config_path('linkedinsharecontent.php'),
        ]);
    }
    public function register()
    {
    }
}
