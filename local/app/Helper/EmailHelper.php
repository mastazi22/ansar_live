<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 7/22/2018
 * Time: 5:18 PM
 */

namespace App\Helper;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

trait EmailHelper
{
        public function sendEmail($view,$data,$to,$subject="",$attachment=null){
            return Mail::send($view,$data,function($message) use($to,$subject,$attachment){
                $message->to($to);
                $message->subject($subject);
                if($attachment&&File::exists($attachment)){
                    $message->attach($attachment);
                }
            });

        }
        public function sendEmailRaw($text,$to,$subject="",$attachment=null){
            return Mail::raw($text,function($message) use($to,$subject,$attachment){
                $message->to($to);
                $message->subject($subject);
                if($attachment&&File::exists($attachment)){
                    $message->attach($attachment);
                }
            });
        }
}