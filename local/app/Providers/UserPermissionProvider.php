<?php

namespace App\Providers;

use App\Helper\UserPermission;
use Illuminate\Support\ServiceProvider;

class UserPermissionProvider extends ServiceProvider
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
        $this->app->singleton('UserPermission',function(){
            return new UserPermission();
        });
    }
}
