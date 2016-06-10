<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

class CheckAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->guest()) {
            If($request->ajax()){
                return Response::json(['status'=>'logout','loc'=>action('UserController@login')]);
            }
            return redirect()->action('UserController@login');
        }
        return $next($request);
    }
}
