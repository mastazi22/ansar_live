<?php

namespace App\modules\HRM\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

class PhotoUploadController extends Controller
{

    //
    public function uploadPhotoSignature(){

        return View::make('HRM::Entryform.upload_photo_signature');

    }
    public function uploadOriginalInfo(){


        return View::make('HRM::Entryform.upload_original_info');
    }

    public function storePhoto(Request $request){
        $rules = [
            'file'=>'mimes:jpeg,jpg'
        ];
        $this->validate($request,$rules);
        $path = storage_path('data/photo');
        if(!File::exists($path)) File::makeDirectory($path,0777,true);
        $file = $request->file('file');
        if(File::exists($path.'/'.$file->getClientOriginalName())){
            File::delete($path.'/'.$file->getClientOriginalName());
        }
        try {
            Image::make($file)->save($path . '/' . $file->getClientOriginalName());
        }catch (\Exception $e){

        }
    }
    public function storeSignature(Request $request){
        $rules = [
            'file'=>'mimes:jpeg,jpg'
        ];
        $this->validate($request,$rules);
        $path = storage_path('data/signature');
        if(!File::exists($path)) File::makeDirectory($path,0777,true);
        $file = $request->file('file');
        if(File::exists($path.'/'.$file->getClientOriginalName())){
            File::delete($path.'/'.$file->getClientOriginalName());
        }
        try {
            Image::make($file)->save($path . '/' . $file->getClientOriginalName());
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function storeOriginalFrontInfo(Request $request){
        $rules = [
            'file'=>'mimes:jpeg,jpg'
        ];
        $this->validate($request,$rules);
        $path = storage_path('data/orginalinfo/frontside');
        if(!File::exists($path)) File::makeDirectory($path,0777,true);
        $file = $request->file('file');
        if(File::exists($path.'/'.$file->getClientOriginalName())){
            File::delete($path.'/'.$file->getClientOriginalName());
        }
        try {
            $fImage = Image::make($file);
            $fWidth = ($fImage->width()*75)/100;
            $fImage->resize($fWidth,null,function($constraint){
                $constraint->aspectRatio();
            })->save($path . '/' . $file->getClientOriginalName());
        }catch (\Exception $e){

        }
    }
    public function storeOriginalBackInfo(Request $request){
        $rules = [
            'file'=>'mimes:jpeg,jpg'
        ];
        $this->validate($request,$rules);
        $path = storage_path('data/orginalinfo/backside');
        if(!File::exists($path)) File::makeDirectory($path,0777,true);
        $file = $request->file('file');
        if(File::exists($path.'/'.$file->getClientOriginalName())){
            File::delete($path.'/'.$file->getClientOriginalName());
        }
        try {
            $bImage = Image::make($file);
            $fWidth = ($bImage->width()*75)/100;
            $bImage->resize($fWidth,null,function($constraint){
                $constraint->aspectRatio();
            })->save($path . '/' . $file->getClientOriginalName());
            Image::make($file)->save($path . '/' . $file->getClientOriginalName());
        }catch (\Exception $e){

        }
    }
}
