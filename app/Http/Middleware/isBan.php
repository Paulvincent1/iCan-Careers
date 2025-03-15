<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isBan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->user()->ban){

           Auth::logout();

           $request->session()->invalidate();

           $request->session()->regenerateToken();

           return redirect('/')->with('error', 'Your account has been banned.');
        }
        return $next($request);
    }
}
