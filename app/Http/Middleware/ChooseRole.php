<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChooseRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   //TODO: ADD THIS MIDDLEWARE TO ALL THE AUTH ROUTES.
        if(!$request->user()->roles()->first()){
            return redirect()->route('choose.role');
        }

        return $next($request);
    }
}
