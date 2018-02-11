<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 2/11/2018
 * Time: 11:58 AM
 */

namespace App\Helper;


trait SMSTrait
{
        public function sendSMS($mobile_no,$message){
            $user = env('SSL_USER_ID','ansarapi');
            $pass = env('SSL_PASSWORD','x83A7Z96');
            $sid = env('SSL_SID','ANSARVDP');
            $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
            $param = "user=$user&pass=$pass&sms[0][0]=$mobile_no&sms[0][1]=" . urlencode($message) . "&sid=$sid";
            $crl = curl_init();
            curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($crl, CURLOPT_URL, $url);
            curl_setopt($crl, CURLOPT_HEADER, 0);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($crl, CURLOPT_POST, 1);
            curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
            $response = curl_exec($crl);
            curl_close($crl);
            return $response;
        }
}