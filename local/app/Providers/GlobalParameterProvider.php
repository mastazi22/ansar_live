<?php

namespace App\Providers;

use App\Helper\GlobalParameter;
use Illuminate\Support\ServiceProvider;

class GlobalParameterProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('GlobalParameter',function(){
            return new GlobalParameter();
        });
    }
}
