<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if($request->route()->getPrefix()=='SD'&&!($user->type==11||$user->type==22)) return response()->view('errors.401');
        if($user->userPermission->permission_type==0){
            if(is_null($user->userPermission->permission_list)){
                if($request->ajax()) return response()->json(['status'=>'forbidden','url'=>$request->fullUrl()]);
                return response()->view('errors.401');
            }
            else{
                $permission = json_decode($user->userPermission->permission_list);
                if(!is_null($request->route()->getName())&&!in_array($request->route()->getName(),$permission)){
                    if($request->ajax()) return response()->json([]);
                    return response()->view('errors.401');
                }
            }
        }
        return $next($request);
    }
}
