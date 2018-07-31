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
        $sub_module = config("modules.sub_module");
        foreach($modules as $module ){
            if(file_exists(__DIR__.'/'.$module.'/routes.php')){
                include __DIR__.'/'.$module.'/routes.php';
//                echo $module;
            }
            if(file_exists(__DIR__.'/'.$module.'/api.php')){
                include __DIR__.'/'.$module.'/api.php';
//                echo $module;
            }
            if(is_dir(__DIR__.'/'.$module.'/Views')){
                $this->loadViewsFrom(__DIR__.'/'.$module.'/Views',$module);
            }
            if(file_exists(__DIR__.'/'.$module.'/breadcrumbs.php')){
                include __DIR__.'/'.$module.'/breadcrumbs.php';
            }
            if(isset($sub_module[$module])){
                foreach ($sub_module[$module]["sub_module"] as $sub){
                    if(file_exists(__DIR__.'/'.$module.'/subModule/'.$sub.'/routes.php')){
                        include __DIR__.'/'.$module.'/subModule/'.$sub.'/routes.php';
//                echo $module;
                    }
                    if(file_exists(__DIR__.'/'.$module.'/subModule/'.$sub.'/api.php')){
                        include __DIR__.'/'.$module.'/subModule/'.$sub.'/api.php';
//                echo $module;
                    }
                    if(is_dir(__DIR__.'/'.$module.'/subModule/'.$sub.'/Views')){
                        $this->loadViewsFrom(__DIR__.'/'.$module.'/subModule/'.$sub.'/Views',$module.".".$sub);
                    }
                    if(file_exists(__DIR__.'/'.$module.'/subModule/'.$sub.'/breadcrumbs.php')){
                        include __DIR__.'/'.$module.'/subModule/'.$sub.'/breadcrumbs.php';
                    }
                }
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
