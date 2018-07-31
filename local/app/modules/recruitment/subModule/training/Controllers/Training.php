<?php

namespace App\modules\recruitment\subModule\training\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Training extends Controller
{
    private $module_name = "recruitment.training";
    public function index(){
        return view("$this->module_name::index");
    }
}
