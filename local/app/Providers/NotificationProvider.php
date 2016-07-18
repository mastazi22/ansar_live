<?php

namespace App\Providers;

use App\Helper\ForgetPassword;
use Illuminate\Support\ServiceProvider;

class NotificationProvider extends ServiceProvider
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
        $this->app->singleton('Notification',function(){
           return new ForgetPassword;
        });
    }
}
