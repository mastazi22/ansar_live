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
        public function sendEmail($view,$data,$to,$cc=null,$subject="",$attachment=null){
            $headers = [];
            Mail::send($view,$data,function($message) use($to,$subject,$attachment,$cc,&$headers){
                $message->to($to);
                $message->replyTo(config('mail.from.address'),config('mail.from.name'));
                $message->sender(config('mail.from.address'),config('mail.from.name'));
                if($cc){
                    if(is_array($cc)){
                        foreach ($cc as $c){
                            if(filter_var($c,FILTER_VALIDATE_EMAIL)){
                                $message->cc($c);
                            }
                        }
                    } elseif (filter_var($cc,FILTER_VALIDATE_EMAIL)){
                        $message->cc($cc);
                    }
                }
                $message->subject($subject);
                if($attachment&&File::exists($attachment)){
                    $message->attach($attachment);
                }
                $message->priority(3);
                $message->getSwiftMessage()
                    ->getHeaders()
                    ->addTextHeader('X-Mailer', "PHP". phpversion());
                $headers = $message->getSwiftMessage()
                    ->getHeaders();
            });
            return $headers;
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