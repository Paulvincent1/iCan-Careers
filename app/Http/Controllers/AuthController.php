<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function Illuminate\Log\log;

class AuthController extends Controller
{
    public function registerCreate(Request $request){
        return inertia('Authentication/Register');
    }

    public function register(Request $request){
        $fields = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'role' => 'required'
            ]
            );

            $user = User::create($fields);
            $role = Role::where('name', $fields['role'])->first();
            

            $user->roles()->attach($role->id);

            return redirect()->route('login')->with('status', 'Successfuly registered!');
    }

    public function loginIndex(){
        return inertia('Authentication/Login', ['status' => session('status')]);
    }

    public function login(Request $request){
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if(Auth::attempt($fields)){
            $request->session()->regenerate();

            $user = Auth::user();

            if(!$user->workerProfile){  
                return redirect()->route('create.profile');
            }
            
            return redirect()->intended();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function forgotPasswordIndex(Request $request){
        return inertia('Authentication/ForgotPassword', [
            'status' => $request->session()->get('status')
        ]);
    }

    public function forgotPasswordSendVerification(Request $request){
        $request->validate(['email' => 'required|email']);
 
        $status = Password::sendResetLink(
            $request->only('email')
        );
     
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordIndex(Request $request){

        return inertia('Authentication/ResetPassword', ['token' => $request->route('token'), 'email' => $request->email, 'status' => $request->session()->get('status')]);
    }   

    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
     
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );
     
        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }


    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Inertia::clearHistory();

        return redirect()->route('login');
    }


}
