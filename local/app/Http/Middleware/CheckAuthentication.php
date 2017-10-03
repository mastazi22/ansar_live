<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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
            Session::put('redirect_url',$request->url());
            If($request->ajax()){
                return Response::json(['status'=>'logout','loc'=>action('UserController@login')]);
            }
            return redirect()->action('UserController@login');
        }
        $input = $request->input();
        $input['action_user_id'] = auth()->user()->id;
        $request->replace($input);
        Log::info("user_name: ".auth()->user()->user_name);
        Log::info("action url : ".$request->url());
        Log::info("request data : ");
        Log::info($request->all());
        return $next($request);
    }
}
