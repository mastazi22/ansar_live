<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 7/26/2016
 * Time: 12:22 AM
 */

namespace App\Helper;


use App\models\User;
use Illuminate\Support\Facades\Auth;

class UserPermission
{
    private $permissionFile = 'permission_list.json';
    private $permissionList;
    private $currentUserPermission;

    public function __construct()
    {
        $permissions = file_get_contents(storage_path("user/permission/{$this->permissionFile}"));
        $this->permissionList = collect(json_decode($permissions));
        $this->currentUserPermission = Auth::user()->userPermission->permission_list;
    }

    public function getPermissionList()
    {
        return $this->permissionList->all();
    }

    public function isPermissionExists($name)
    {
        $this->search = $name;
        foreach ($this->permissionList->all() as $item) {
            $search = collect($item->routes);
            $isFound = $search->search(function ($item, $key) {
                return $item->value == $this->search;
            });
            if (!$isFound) continue;
            return true;
        }
        return false;
    }

    public function isMenuExists($value)
    {
        if (is_null($this->currentUserPermission)) {
            return true;
        }
        $p = json_decode($this->currentUserPermission);
        if (is_array($value)) {
            return $this->checkMenu($value,$p);
        }
        else return in_array($value,$p);
    }

    public function getTotal()
    {
        return $this->permissionList->count();
    }

    public function checkMenu($array,$p)
    {
        foreach($array as $a){
            if($a['route']=="#"){
                return $this->checkMenu($a['children'],$p);
            }
            else if(in_array($a['route'],$p)){
                return true;
            }
        }
        return false;
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

}