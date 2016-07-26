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
        if($request->route()->getPrefix()=='SD'&&!($user->type==11||$user->type==22)) return response()->view('errors.401');
        if(UserPermissionFacades::isPermissionExists($request->route()->getName())) {
            if ($user->userPermission->permission_type == 0) {
                if (is_null($user->userPermission->permission_list)) {
                    if ($request->ajax()) return response("Unauthorized(401)", 401);
                    if (!$request->ajax()) return abort(401);
                } else {
                    $permission = json_decode($user->userPermission->permission_list);
                    if (!is_null($request->route()->getName()) && !in_array($request->route()->getName(), $permission)) {
                        if ($request->ajax()) return response("Unauthorized(401)", 401);
                        if (!$request->ajax()) return response()->view('errors.401');
                    }
                }
            }
        }
        return $next($request);
    }
}
