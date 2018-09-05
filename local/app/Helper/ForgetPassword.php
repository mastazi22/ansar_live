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
        $this->forgetPasswordNotification = ForgetPasswordRequest::all(['user_name', 'created_at']);
        $this->userNotification = UserCreationRequest::with('user')->where('status', 'pending');
    }

    public function getForgetPasswordNotificationTotal()
    {
        return $this->forgetPasswordNotification->count();
    }

    public function getTotalUserRequest()
    {
        return $this->userNotification->count();
    }

    public function getForgetPasswordNotification()
    {
        return $this->forgetPasswordNotification->take(5);
    }

    public function getUserRequestNotification()
    {
        return $this->userNotification->limit(5)->get();
    }

    public function getAllUserRequestNotification($id)
    {
        if ($id) return $this->userNotification->where('id',$id)->get();
        else return $this->userNotification->get();
    }

    public function getAllForgetPasswordNotification()
    {
        return $this->forgetPasswordNotification;
    }


}