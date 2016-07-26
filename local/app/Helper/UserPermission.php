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

    public function __construct()
    {
        $permissions = file_get_contents(storage_path("user/permission/{$this->permissionFile}"));
        $this->permissionList = collect(json_decode($permissions));
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

    public function getTotal()
    {
        return $this->permissionList->count();
    }

    public function getPageItem($page, $count)
    {
        return $this->permissionList->forPage($page, $count)->all();
    }

    public function getCurrentUserPermission()
    {
        $p = Auth::user()->userPermission->permission_list;
        if(is_null($p)){
            return null;
        }
        else return json_decode($p);
    }

    public function getUserPermission($id)
    {
        $p = User::find($id)->userPermission->permission_list;
        //var_dump($p);
        if (!$p) {
            return null;
        }
        else return json_decode($p);
    }

}