<?php

namespace App\Http\Controllers;

use App\models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiUserController extends Controller
{
    //
    public function login(Request $request){
        $rules = [
            'user_name'=>'required',
            'password'=>'required'
        ];
        $validator = Validator::make($request->input(),$rules);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        try{
            if(!$token=JWTAuth::attempt($request->only(['user_name','password']))){
                return response()->json(['message'=>'Invalid user name or password'],401);
            }
//            $user = JWTAuth::toUser($token);
            $user = auth()->user();
            if($user->status!=1){
                JWTAuth::invalidate($token);
                return response()->json(['message'=>'User is BLOCKED'],401);
            }
            $user = User::with(['usertype','userProfile','userParent'])->where('id',$user->id)->first();
        }catch (JWTException $e){
            return response()->json(['message'=>$e->getMessage()],500);
        }

        return response()->json(compact('token','user'));
    }
}
