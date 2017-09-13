<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 9/13/2017
 * Time: 4:53 PM
 */

namespace App\Helper;


use App\modules\HRM\Models\SystemSetting;

class SystemSettingHelper
{
    static  $TRANSFER_POLICY = 'transfer_policy';
    public static function getValue($key){
        switch ($key){
            case self::$TRANSFER_POLICY:
                $settings = SystemSetting::where('setting_slug',$key)->first();
                return ['data'=>explode(',',$settings->setting_value),'status'=>$settings->active];
        }

    }
}