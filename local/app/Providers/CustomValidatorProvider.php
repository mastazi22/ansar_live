<?php

namespace App\Providers;

use App\Helper\CustomValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class CustomValidatorProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Validator::resolver( function( $translator, $data, $rules, $messages ) {
            return new CustomValidation($translator,$data,$rules,$messages);
        } );
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
