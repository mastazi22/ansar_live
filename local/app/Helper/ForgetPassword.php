<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 7/18/2016
 * Time: 5:05 PM
 */

namespace App\Helper;


use App\modules\HRM\Models\ForgetPasswordRequest;

class ForgetPassword
{
    private $notification = '';

    /**
     * ForgetPassword constructor.
     */
    public function __construct()
    {
        $this->notification = ForgetPasswordRequest::all(['user_name']);
    }
    public function getTotal(){
        return $this->notification->count();
    }
    public function getNotification(){
        return $this->notification->take(5);
    }
    public function getAllNotification(){
        return $this->notification;
    }


}