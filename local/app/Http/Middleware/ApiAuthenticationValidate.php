<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthenticationValidate
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
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message'=>'Invalid User'], 401);
            }
            if($user->status!=1){
                JWTAuth::invalidate(JWTAuth::getToken());
                return response()->json(['message'=>'User is BLOCKED'],403);
            }
        } catch (TokenExpiredException $e) {

            return response()->json(['message'=>'token expired'], 403);

        } catch (TokenInvalidException $e) {

            return response()->json(['message'=>'token invalid'], 403);

        } catch (JWTException $e) {

            return response()->json(['message'=>'token absent'], 401);

        }
        $input = $request->input();
        $input['action_user_id'] = $user->id;
        $request->replace($input);
        return $next($request);
    }
}
