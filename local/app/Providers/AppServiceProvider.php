<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Validator::extend('array',function($attribute,$value,$parameter,$validator){

            return is_array($value);

        });
        Validator::extend('array_length_max',function($attribute,$value,$parameter,$validator){

            $length = count($value);
            $max = array_get($validator->getData(),$parameter[0]);
            Log::info($max);
            return $length<=$max;
        });
        Validator::extend('array_length_min',function($attribute,$value,$parameter,$validator){

            $length = count($value);
            $min = intval($parameter[0]);
            Log::info($min);
            return $length>=$min;
        });
        Validator::extend('array_type',function($attribute,$value,$parameter,$validator){
            $type = $parameter[0];
            Log::info($type);
            switch($type){
                case 'int':
                    foreach($value as $v){
                        if(!is_int($v)) return false;
                    }
                break;
            }

            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
