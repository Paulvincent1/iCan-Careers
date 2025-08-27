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

           return redirect('/login')->withErrors([
                    'email' => 'Your account is banned, contact our team to appeal if you are innocent',
                ])->onlyInput('email');
        }
        return $next($request);
    }
}
