<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class YourMiddlewareName
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Deine Middleware-Logik hier

        return $next($request);
    }
}
