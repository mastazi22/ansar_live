<?php

namespace App\modules\SD\Provider;

use App\modules\SD\Helper\DemandConstant;
use Illuminate\Support\ServiceProvider;

class DemandConstantProvider extends ServiceProvider
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
        $this->app->bind('DemandConstant',function(){
            return new DemandConstant();
        });
    }
}
