<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    //
    protected $table = 'tbl_system_setting';
    public $fillable = ['setting_value','active','description'];

    public function getValueAsString(){
        $data = explode(',',$this->setting_value);
        ///return count($data);
        $units = District::whereIn('id',$data)->get()->pluck('unit_name_bng');
        $view = view('HRM::Partial_view.block_view',['data'=>$units])->render();
        return $view;

    }
}
