<?php

namespace App\Providers;

use App\Helper\LanguageConverter;
use Illuminate\Support\ServiceProvider;

class LanguageConvertorProvider extends ServiceProvider
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
        $this->app->bind('LanguageConverter',function(){
            return new LanguageConverter();
        });
    }
}
