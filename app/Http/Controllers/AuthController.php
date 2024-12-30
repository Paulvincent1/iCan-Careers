<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerCreate(Request $request){
        return inertia('Register');
    }

    public function register(Request $request){
        $fields = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed'
            ]
            );

            $user = User::create($fields);

            return inertia('Login');
    }

    public function loginIndex(){
        return inertia('Login');
    }

    public function login(Request $request){
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if(Auth::attempt($fields)){
            $request->session()->regenerate();

            return redirect()->intended('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
