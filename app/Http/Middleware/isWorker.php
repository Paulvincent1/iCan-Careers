<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isWorker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasWorkerRole = $request->user()->roles()->whereIn('name', ['PWD', 'Senior'])->exists();

        if($hasWorkerRole){

            return $next($request);
        }

        return redirect()->intended();
       
    }
}
