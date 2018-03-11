<?php

namespace App\Http\Middleware;

use App\modules\HRM\Models\RequestDumper;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class HRMMiddleware
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
//        return $request->url();
       if(!$request->ajax()){
           Session::forget('module');
           Session::put('module','HRM');
       }
        return $next($request);
    }
}
