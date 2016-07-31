<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class ManageDatabase
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
        if(!is_null(request()->route())) {
            $prefix = strtolower(request()->route()->getPrefix());
            if (!is_null($prefix)) Config::set('database.default', $prefix);
            else Config::set('database.default', 'hrm');
        }
        else Config::set('database.default', 'hrm');
        return $next($request);
    }
}
