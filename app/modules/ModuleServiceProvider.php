<?php

namespace App\modules;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $modules = config("modules.module");
        foreach($modules as $module ){
            if(file_exists(__DIR__.'/'.$module.'/routes.php')){
                include __DIR__.'/'.$module.'/routes.php';
//                echo $module;
            }
            if(is_dir(__DIR__.'/'.$module.'/Views')){
                $this->loadViewsFrom(__DIR__.'/'.$module.'/Views',$module);
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
