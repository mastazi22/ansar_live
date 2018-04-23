<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 7/18/2016
 * Time: 5:05 PM
 */

namespace App\Helper;


use App\models\UserCreationRequest;
use App\modules\HRM\Models\ForgetPasswordRequest;

class ForgetPassword
{
    private $forgetPasswordNotification = '';
    private $userNotification = '';

    /**
     * ForgetPassword constructor.
     */
    public function __construct()
    {
        $this->forgetPasswordNotification = ForgetPasswordRequest::all(['user_name','created_at']);
        $this->userNotification = collect(UserCreationRequest::with('user')->where('status','pending')->get());
    }
    public function getForgetPasswordNotificationTotal(){
        return $this->forgetPasswordNotification->count();
    }
    public function getTotalUserRequest(){
        return $this->userNotification->count();
    }
    public function getForgetPasswordNotification(){
        return $this->forgetPasswordNotification->take(5);
    }
    public function getUserRequestNotification(){
        return $this->userNotification->take(5);
    }
    public function getAllUserRequestNotification(){
        return $this->userNotification;
    }
    public function getAllForgetPasswordNotification(){
        return $this->forgetPasswordNotification;
    }


}