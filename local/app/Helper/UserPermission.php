<?php
/**
 * Created by PhpStorm.
 * User: arafat
 * Date: 7/26/2016
 * Time: 12:22 AM
 */

namespace App\Helper;


class UserPermission
{
    private $permissionFile = 'permission_list.json';
    private $permissionList;
    public function __construct()
    {
        $permissions = file_get_contents(storage_path("user/permission/{$this->permissionFile}"));
        $this->permissionList = collect(json_decode($permissions));
    }
    public function getPermissionList(){
        return $this->permissionList->all();
    }
    public function isPermissionExists($name){
        $this->search = $name;
        foreach ($this->permissionList->all() as $item) {
            $search = collect($item->routes);
            $isFound = $search->search(function($item,$key){
               return $item->value==$this->search;
            });
            if(!$isFound) continue;
            return true;
        }
        return false;
    }

}