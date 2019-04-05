<?php

namespace App\Http\Middleware;

use App\Helper\Facades\UserPermissionFacades;
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
//        if($request->route()->getPrefix()!='recruitment'&&$user->type==111) return response()->view('errors.401');
//        return UserPermissionFacades::isPermissionExists($request->route()->getName())?"YES":"NO";
        if($request->route()&& UserPermissionFacades::isPermissionExists($request->route()->getName())) {
            if ($user->userPermission->permission_type == 0) {
                if (is_null($user->userPermission->permission_list)) {
                    if ($request->is('api/*')||$request->is('*/api/*')) return response()->json(['message'=>'insufficient permission'], 401);
                    else if ($request->ajax()) return response("Unauthorized(401)", 401);
                    else return abort(401);
                } else {
                    $permission = json_decode($user->userPermission->permission_list);
//                    return UserPermissionFacades::isUserMenuExists($request->route()->getName(), $permission)?"YES":"NO";
                    if (!is_null($request->route()->getName()) && !UserPermissionFacades::isUserMenuExists($request->route()->getName(), $permission)) {
                        if ($request->is('api/*')||$request->is('*/api/*')) return response()->json(['message'=>'insufficient permission'], 401);
                        else if ($request->ajax()) return response("Unauthorized(401)", 401);
                        else return response()->view('errors.401');
                    }
                }
            }
        }
        return $next($request);
    }
}
