<?php

namespace App\Http\Controllers;

use App\Models\EmployerSubscriptionInvoice;
use App\Models\Role;
use App\Models\User;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function Illuminate\Log\log;

class AuthController extends Controller
{

    public function __construct(public InvoiceService $invoiceService)
    {

    }

    public function registerCreate(Request $request){
        return inertia('Authentication/Register');
    }

    public function sendCode(Request $request){
        $fields = $request->validate([
                'email' => 'required|email|unique:users,email',
        ]);

        

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


            if($fields['role'] === 'Employer'){

                DB::beginTransaction();

                $user = User::create($fields);
                $role = Role::where('name', $fields['role'])->first();


                $user->roles()->attach($role->id);
                // creating a free tier subscription if the user is employer
                $user->employerSubscription()->create([
                    'subscription_type' => 'Free',
                    'start_date' => Carbon::now(),
                ]);


                try {


                    $externalIdProTier =  'INV-'.uniqid();
                    $externalIdPremiumTier =  'INV-'.uniqid();


                        // creating pro tier invoice
                    $proTierInvoice = $this->invoiceService
                    ->createInvoice(
                        externalId: $externalIdProTier,
                        description: 'Pro Tier Subscription (Monthly)',
                        items:  [
                            [
                            'description' => 'Pro Tier Subscription',
                            'rate' => 3999,
                            'hours' => 1,
                            ]
                            ],
                        duration: Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23,59,0))
                        );
                        // dd($proTierInvoice->getInvoiceUrl());

                        $user->employerSubscriptionInvoices()->create([
                            'external_id' =>  $externalIdProTier,
                            'invoice_id' =>  $proTierInvoice->getId(),
                            'description' => 'Pro Tier Subscription (Monthly)',
                            'invoice_url' =>  $proTierInvoice->getInvoiceUrl(),
                            'subscription_type' => 'Pro',
                            'duration' => now(),
                        ]);




                        // creating premium tier invoice
                    $premiumTierInvoice = $this->invoiceService
                    ->createInvoice(
                        externalId: $externalIdPremiumTier,
                        description: 'Premium Tier Subscription (Anually)',
                        items: [
                            [
                            'description' => 'Premium Tier Subscription',
                            'rate' => 5699,
                            'hours' => 1,
                            ]
                            ],
                            duration: Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23,59,0))
                        );

                    $user->employerSubscriptionInvoices()->create([
                        'external_id' =>  $externalIdPremiumTier,
                        'invoice_id' =>  $proTierInvoice->getId(),
                        'description' => 'Premium Tier Subscription (Anually)',
                        'invoice_url' =>  $premiumTierInvoice->getInvoiceUrl(),
                        'subscription_type' => 'Premium',
                        'duration' => now(),
                    ]);

                    DB::commit();

                }catch(Exception $e){

                    DB::rollBack();

                    return redirect()->back()->withErrors(['message' => 'Error creating account, Please try again.']);

                }


            }



            if($fields['role'] === 'PWD' || $fields['role'] === 'Senior'){

                $user = User::create($fields);
                $role = Role::where('name', $fields['role'])->first();


                $user->roles()->attach($role->id);

                $user->balance()->create([
                    'balance' => 0,
                    'unsettlement' => 0,
                ]);
                $user->workerBasicInfo()->create();

            }

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

            $userRole = '';
            foreach($user->roles as $role){
                $userRole = $role->name;
            }

            if($userRole === 'Senior' || $userRole === 'PWD'){
                if(!$user->workerProfile){
                    return redirect()->route('create.profile');
                }
                return redirect()->route('worker.dashboard');
            }

            if($userRole === 'Employer'){
                if(!$user->employerProfile){

                    return redirect()->route('create.profile.employer');
                }

                return redirect()->route('employer.dashboard');
            }

            if($userRole === 'Admin'){
                return redirect()->route('admin.dashboard');
            }

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
