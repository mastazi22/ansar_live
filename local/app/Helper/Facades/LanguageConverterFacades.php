<?php
/**
 * Created by PhpStorm.
 * User: darksider
 * Date: 12/20/2015
 * Time: 1:42 PM
 */

namespace App\Helper\Facades;


use Illuminate\Support\Facades\Facade;

class LanguageConverterFacades extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'LanguageConverter';
    }
}