<?php

namespace App\Http\Controllers;

use App\Helper\SMSTrait;
use App\models\TempUser;
use App\models\User;
use App\modules\HRM\Models\PersonalInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiUserController extends Controller
{
    use SMSTrait;
    //
    public function login(Request $request){
        $rules = [
            'login_type'=>['required','regex:/^(mn)|(up)$/'],
            'user_name'=>'required_if:login_type,==,up',
            'password'=>'required_if:login_type,==,up',
            'mobile_no'=>'required_if:login_type,==,mn'
        ];
        $validator = Validator::make($request->input(),$rules);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        if($request->login_type=="mn"){
            $ansar = PersonalInfo::where('mobile_no_self',$request->mobile_no)->first();
            if($ansar && ($ansar->designation_id==2||$ansar->designation_id==3)){
                $time = time().'';
                $code = substr($time,-6);
                $temp_user = new TempUser;
                $temp_user->verification_code = $code;
                $temp_user->full_name = $ansar->ansar_name_eng;
                $temp_user->ansar_id = $ansar->ansar_id;
                $temp_user->save();
                $this->sendSMS($ansar->mobile_no_self,"Your verification code: $code");
                $ansar_id = $ansar->ansar_id;
                return response()->json(compact('code','ansar_id'));
            }
            else{
                return response()->json(['message'=>'Your not a valid user'],401);
            }
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
        $user->userProfile->profile_image = route('HRM.api.user_profile_image',['id'=>$user->id]);
        return response()->json(compact('token','user'));
    }
    public function verifyCode(Request $request){
        $rules = [

            'code'=>'required',
            'ansar_id'=>'required'
        ];
        $validator = Validator::make($request->input(),$rules);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $verification_code = $request->code;
        $ansar_id = $request->ansar_id;
        $temp_user = TempUser::where(compact('verification_code','ansar_id'))->first();
        if($temp_user){
            if(Carbon::parse($temp_user->created_at)->addMinutes(30)->gte(Carbon::now())) {
                $user_name = Str::random(6);
                $t_pass = Str::random(6);
                $password = Hash::make($t_pass);
                $return_verification_code = $verification_code;
                $temp_user->update(compact('user_name', 'password', 'return_verification_code'));
                Config::set('jwt.user', 'App\models\TempUser');
                config(['auth.guards.web.provider' => 'tempUser', 'jwt.user' => 'App\models\TempUser']);
                Auth::getProvider()->setModel(TempUser::class);
                DB::enableQueryLog();
                try {
                    if (!$token = JWTAuth::attempt(['user_name' => $user_name, 'password' => $t_pass])) {
                        return DB::getQueryLog();
                        return response()->json(['message' => 'Invalid user name or password'], 401);
                    }
//            $user = JWTAuth::toUser($token);
                    $user = auth()->user();
                } catch (JWTException $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
                return response()->json(compact('token', 'user'));
            }else{
                return response()->json(['message'=>'Verification code expire'],401);
            }

        }
        return response()->json(['message'=>'Invalid code'],401);
    }
}
