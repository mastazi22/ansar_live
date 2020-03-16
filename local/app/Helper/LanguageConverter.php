<?php

namespace App\Helper;

class LanguageConverter
{
    private $engNumeric = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    private $bngNumeric = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    private $bngMonth = ["জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগস্ট", "সেপ্টেম্বর", "অক্টোবর", "নভেম্বর", "ডিসেম্বর"];
    
    public function engToBng($str, $enableBngMonth = false)
    {
        return str_replace($this->engNumeric, $this->bngNumeric, $str);
    }

    public function bngToEng($str)
    {
        return str_replace($this->bngNumeric, $this->engNumeric, $str);
    }

    /**
     * Format must be in day/month/year
     * @param $date
     * @param string $separator
     * @return string
     */
    public function engToBngWS($date, $separator = "/")
    {
        $date = explode('/', $date);
        $day = str_replace($this->engNumeric, $this->bngNumeric, $date[0]);
        $year = str_replace($this->engNumeric, $this->bngNumeric, $date[2]);
        $month = $this->bngMonth[$date[1] - 1];
        return $day . " " . $month . " " . $year;
    }
}