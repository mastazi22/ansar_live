<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 7/26/2016
 * Time: 12:22 AM
 */

namespace App\Helper;


use App\models\User;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\KpiGeneralModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class UserPermission
{
    private $permissionFile = 'test_list.json';
    private $permissionList;
    private $currentUserPermission;
    private $search;

    public function __construct()
    {
        $permissions = file_get_contents(storage_path("user/permission/{$this->permissionFile}"));
        $this->permissionList = Config::get('permission.permission_list');
        if(Auth::user())$this->currentUserPermission = Auth::user()->userPermission->permission_list;
        $this->search = '';
    }

    public function getPermissionList()
    {
        return $this->permissionList->all();
    }

    public function isPermissionExists($name)
    {
        $status = false;
        foreach($this->permissionList as $search){
            $results = explode(",",$search);
            $status = in_array($name,$results);
            if($status) break;
        }
        return $status;
    }
    public function userPermissionExists($name)
    {
        if(!Auth::user())return true;
        if(Auth::user()->type==11) return true;
        if (is_null($this->currentUserPermission)) {
            if(Auth::user()->type==11||Auth::user()->type==33)
                return true;
            else return false;
        }
        $status = false;
        $p = $this->currentUserPermission;
        foreach(json_decode($p) as $search){
            $results = explode(",",$search);
            $status = in_array($name,$results);
            if($status) break;
        }
        return $status;
    }
    public function isUserMenuExists($name,$p)
    {
        if(!Auth::user())return false;
//        Log::info("Found:".$name);
        if(Auth::user()->type==11) return true;
        $status = false;
        foreach($p as $search){
            $results = explode(",",$search);
           // Log::info($results);
           // Log::info("NAME ===".$name);
            $status = in_array($name,$results);
            if($status) {
                Log::info("FOUND ".$name);
                break;
            }

        }

        return $status;
    }

    public function isMenuExists($value)
    {

        if(!Auth::user())return false;
        if(Auth::user()->type==11) return true;
        if (is_null($this->currentUserPermission)) {
            if(Auth::user()->type==11||Auth::user()->type==33)
            return true;
            else return false;
        }
        $p = json_decode($this->currentUserPermission);
        if (is_array($value)) {
            //Log::info("STATUS :".($this->checkMenu($value,$p)?"true":"false"));
            return $this->checkMenu($value,$p);
        }
        else {
            //Log::info("STATUS NOT:".($this->isUserMenuExists($value,$p)?"true":"false"));
            return $this->isUserMenuExists($value,$p);
        }

    }

    public function getTotal()
    {
        return $this->permissionList->count();
    }

    public function checkMenu($array,$p)
    {
        Log::info($array);
        foreach($array as $a){
            if($a['route']=="#"){
                //Log::info($a['children']);
                $this->checkMenu($a['children'],$p);
            }
            else if($this->isUserMenuExists($a['route'],$p)){
                return true;
            }
        }
        //return true;
    }

    public function getPageItem($page, $count)
    {
        return $this->permissionList->forPage($page, $count)->all();
    }

    public function getCurrentUserPermission()
    {
        if (is_null($this->currentUserPermission)) {
            return null;
        } else return json_decode($this->currentUserPermission);
    }

    public function getUserPermission($id)
    {
        $p = User::find($id)->userPermission->permission_list;
        //var_dump($p);
        if (!$p) {
            return null;
        } else return json_decode($p);
    }

    public function isAnsarEmbodied($ansar_Id){

        if(Auth::user()){
            if(Auth::user()->type==11) return true;
            else if(Auth::user()->type==22){

                $kpi = KpiGeneralModel::where('unit_id',Auth::user()->district_id)->pluck('id');
                return EmbodimentModel::where('ansar_id',$ansar_Id)->whereIn('kpi_id',$kpi)->exists();


            }
            else return false;
        }
        return true;
    }

}