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
        $roles = [];
        $isWorker = true;
        foreach($request->user()->roles()->where('name', 'Senior')->orWhere('name','PWD')->get() as $role){

            $roles[]= $role->name;
        }
        foreach($roles as $role){ 
            if($role === 'Senior' || $role === 'PWD'){
                $isWorker = true;
            }else{
                $isWorker = false;
                break;
            }
        }
        if($isWorker){
            return $next($request);
        }else{
           return redirect()->route('worker.dashboard');
        }
    }
}
