<?php

namespace App\Helper\Facades;
use Illuminate\Support\Facades\Facade;

class GlobalParameterFacades extends Facade
{
    public static function getFacadeAccessor(){
        return 'GlobalParameter';
    }
}