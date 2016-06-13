<?php
/**
 * Created by PhpStorm.
 * User: darksider
 * Date: 12/20/2015
 * Time: 1:31 PM
 */

namespace App\Helper;


class LanguageConverter
{
    private $engNumeric = ['0','1','2','3','4','5','6','7','8','9'];
    private $bngNumeric = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    public function engToBng($str){
        return str_replace($this->engNumeric,$this->bngNumeric,$str);
    }
    public function bngToEng($str){
        return str_replace($this->bngNumeric,$this->engNumeric,$str);
    }
}