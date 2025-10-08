<?php

namespace App\Http\Controllers;

use App\Mail\SendCode;
use App\Models\EmailVerification;
use App\Models\EmployerSubscriptionInvoice;
use Dacastro4\LaravelGmail\Services\Message\Mail;
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
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function Illuminate\Log\log;

class AuthController extends Controller
{

    public function __construct(public InvoiceService $invoiceService) {}

    public function registerCreate(Request $request)
    {
        return inertia('Authentication/Register');
    }

    public function sendCode(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ]);

        $code = rand(100000, 999999);

        $emailVerification = EmailVerification::updateOrCreate(
            ['email' => $fields['email']],
            [
                'name' => $fields['name'],
                'role' => $fields['role'],
                'verification_code' => $code,

            ]
        );

        $mail = new Mail;
        $mail->to($emailVerification->email)
         ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
         ->subject('Verification Code')
         ->view('mail.send-code', [
             'emailVerification' => $emailVerification,
         ])
         ->send();


    }

    public function verifyEmail($code)
    {
        $emailVerification = EmailVerification::where('verification_code', $code)->first();

        if (!$emailVerification) {
            return redirect()->route('login')->withErrors([
                'message' => 'Invalid verification code or code has expired.'
            ]);
        }

        // ✅ Expiry check using created_at
        if ($emailVerification->created_at->addMinutes(10)->isPast()) {
            $emailVerification->delete(); // optional cleanup
            return redirect()->route('login')->withErrors([
                'message' => 'Verification code has expired. Please request a new one.'
            ]);
        }



        // Check if user already exists
        $user = User::where('email', $emailVerification->email)->first();

        if ($user) {
            // User exists - just verify email and login
            $user->update(['email_verified_at' => now()]);
            $emailVerification->delete();
            Auth::login($user);

            return redirect()->route('choose.role')->with([
                'success' => 'Email successfully verified!'
            ]);
        }

        // USER DOESN'T EXIST - CREATE THE USER AUTOMATICALLY!
        // Generate a temporary password (users can change it later)
        $temporaryPassword = Str::random(12);

        if ($emailVerification->role === 'Employer') {
            DB::beginTransaction();

            $user = User::create([
                'name' => $emailVerification->name,
                'email' => $emailVerification->email,
                'password' => Hash::make($temporaryPassword),
                'email_verified_at' => now(), // Mark as verified
            ]);

            $role = Role::where('name', $emailVerification->role)->first();
            $user->roles()->attach($role->id);

            // Creating a free tier subscription if the user is employer
            $user->employerSubscription()->create([
                'subscription_type' => 'Free',
                'start_date' => Carbon::now(),
            ]);

            try {
                $externalIdProTier =  'INV-' . uniqid();
                $externalIdPremiumTier =  'INV-' . uniqid();

                // Creating pro tier invoice
                $proTierInvoice = $this->invoiceService
                    ->createInvoice(
                        externalId: $externalIdProTier,
                        description: 'Pro Tier Subscription (Monthly)',
                        items: [
                            [
                                'description' => 'Pro Tier Subscription',
                                'rate' => 3999,
                                'hours' => 1,
                            ]
                        ],
                        duration: Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23, 59, 0))
                    );

                $user->employerSubscriptionInvoices()->create([
                    'external_id' =>  $externalIdProTier,
                    'invoice_id' =>  $proTierInvoice->getId(),
                    'description' => 'Pro Tier Subscription (Monthly)',
                    'invoice_url' =>  $proTierInvoice->getInvoiceUrl(),
                    'subscription_type' => 'Pro',
                    'duration' => now(),
                ]);

                // Creating premium tier invoice
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
                        duration: Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23, 59, 0))
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
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()->route('register.create')->withErrors([
                    'message' => 'Error creating account. Please try registering again.'
                ]);
            }
        } else {
            // For PWD or Senior roles
            $user = User::create([
                'name' => $emailVerification->name,
                'email' => $emailVerification->email,
                'password' => Hash::make($temporaryPassword),
                'email_verified_at' => now(), // Mark as verified
            ]);

            $role = Role::where('name', $emailVerification->role)->first();
            $user->roles()->attach($role->id);

            $user->balance()->create([
                'balance' => 0,
                'unsettlement' => 0,
            ]);
            $user->workerBasicInfo()->create();
        }

        // Delete the verification code
        $emailVerification->delete();

        // Auto-login the user
        Auth::login($user);

        // Redirect based on role
        if ($emailVerification->role === 'Employer') {
            return redirect()->route('create.profile.employer')->with([
                'success' => 'Account created successfully! Please set your password in account settings.'
            ]);
        } else {
            return redirect()->route('create.profile')->with([
                'success' => 'Account created successfully! Please set your password in account settings.'
            ]);
        }
    }


    public function register(Request $request)
    {
        $fields = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:6',
                'role' => 'required',
                'verification_code' => 'required',
            ]
        );

        $emailVerification = EmailVerification::where('email', $fields['email'])->first();

        // Check expiry
        if ($emailVerification->created_at->addMinutes(10)->isPast()) {
            $emailVerification->delete();
            return redirect()->back()->withErrors([
                'message' => 'Your verification code has expired. Please request a new one.'
            ]);
        }

        if ($fields['verification_code'] != $emailVerification->verification_code) {
            return redirect()->back()->withErrors(
                [
                    'message' => 'The verification code you entered is incorrect. Please check your email and try again.'
                ]
            );
        }


        if ($fields['role'] === 'Employer') {

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


                $externalIdProTier =  'INV-' . uniqid();
                $externalIdPremiumTier =  'INV-' . uniqid();


                // creating pro tier invoice
                $proTierInvoice = $this->invoiceService
                    ->createInvoice(
                        externalId: $externalIdProTier,
                        description: 'Pro Tier Subscription (Monthly)',
                        items: [
                            [
                                'description' => 'Pro Tier Subscription',
                                'rate' => 3999,
                                'hours' => 1,
                            ]
                        ],
                        duration: Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23, 59, 0))
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
                        duration: Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23, 59, 0))
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
            } catch (Exception $e) {

                DB::rollBack();

                return redirect()->back()->withErrors(['message' => 'Error creating account, Please try again.']);
            }
        }



        if ($fields['role'] === 'PWD' || $fields['role'] === 'Senior') {

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

    public function loginIndex()
    {
        return inertia('Authentication/Login', ['status' => session('status')]);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($fields)) {
            $request->session()->regenerate();

            $user = Auth::user();

            $userRole = '';
            foreach ($user->roles as $role) {
                $userRole = $role->name;
            }

            if ($userRole === 'Senior' || $userRole === 'PWD') {
                if (!$user->workerProfile) {
                    return redirect()->route('create.profile');
                }
                return redirect()->route('worker.dashboard');
            }

            if ($userRole === 'Employer') {
                if (!$user->employerProfile) {

                    return redirect()->route('create.profile.employer');
                }

                return redirect()->route('employer.dashboard');
            }

            if ($userRole === 'Admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Login failed. If you can’t remember your password, click "Forgot Password?" to reset it.',
        ])->onlyInput('email');
    }

    public function forgotPasswordIndex(Request $request)
    {
        return inertia('Authentication/ForgotPassword', [
            'status' => $request->session()->get('status')
        ]);
    }

    public function forgotPasswordSendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordIndex(Request $request)
    {

        return inertia('Authentication/ResetPassword', ['token' => $request->route('token'), 'email' => $request->email, 'status' => $request->session()->get('status')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
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


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Inertia::clearHistory();

        return redirect()->route('login');
    }
}
