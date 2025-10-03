<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SocialiteController extends Controller
{

    public function __construct(public InvoiceService $invoiceService) {}

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Try to find user by google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            // If not found, try to find by email
            if (!$user && $googleUser->getEmail()) {
                $user = User::where('email', $googleUser->getEmail())->first();

                // If found by email but google_id not set, update it
                if ($user && !$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'profile_img' => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                    ]);
                }
            }

            // If still no user, create a new one
            if (!$user) {
                $user = User::create([
                    'name'              => $googleUser->getName() ?? 'No Name',
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'profile_img'       => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password'          => bcrypt(Str::random(12)),
                ]);
            }

            // At this point $user is guaranteed to be a User model
            Auth::login($user);

            return redirect()->intended('/dashboard'); // or role-based redirect
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google login failed, please try again.');
        }
    }

    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleAuthentication()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->id)->first();
        $emailUser = User::where('email', $googleUser->email)->first();

        if ($user || $emailUser) {
            $account = $user ?? $emailUser; // whichever is not null

            // If email matched but google_id was not set, update it
            if (!$account->google_id) {
                $account->update([
                    'google_id' => $googleUser->id,
                ]);
            }

            // ✅ Log in the correct account
            Auth::login($account);

            $authUser = Auth::user();
            Inertia::clearHistory();

            if ($authUser->roles()->first()) {
                $userRole = '';
                foreach ($authUser->roles as $role) {
                    $userRole = $role->name;
                }

                if ($userRole === 'Senior' || $userRole === 'PWD') {
                    if (!$authUser->workerProfile) {
                        return redirect()->route('create.profile');
                    }
                    return redirect()->route('worker.dashboard');
                }

                if ($userRole === 'Employer') {
                    if (!$authUser->employerProfile) {
                        return redirect()->route('create.profile.employer');
                    }
                    return redirect()->route('employer.dashboard');
                }

                if ($userRole === 'Admin') {
                    return redirect()->route('admin.dashboard');
                }
            } else {
                return redirect()->route('choose.role');
            }
        } else {
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(Str::random(12)), // use bcrypt
                'google_id' => $googleUser->id,
            ]);

            Auth::login($newUser);
            Inertia::clearHistory();
            return redirect()->route('choose.role');
        }
    }


    public function chooseRole(Request $request)
    {
        if ($request->user()->roles()->first()) {

            return redirect()->back();
        }
        return inertia('Authentication/ChooseRole');
    }

    // TODO: do this controller p
    public function storeRole(Request $request)
    {
        $fields = $request->validate(
            [
                'role' => 'required'
            ]
        );


        if ($fields['role'] === 'Employer') {

            DB::beginTransaction();

            $user = Auth::user();
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
                dd($fields['role']);
                //TODO: SHOW THIS MESSAGE IN CHOOSE ROLE
                return redirect()->back()->withErrors(['message' => 'Error creating account, Please try again.']);
            }
        }



        if ($fields['role'] === 'PWD' || $fields['role'] === 'Senior') {

            $user = Auth::user();
            $role = Role::where('name', $fields['role'])->first();


            $user->roles()->attach($role->id);

            $user->balance()->create([
                'balance' => 0,
                'unsettlement' => 0,
            ]);
            $user->workerBasicInfo()->create();
        }

        if ($fields['role'] === 'PWD' || $fields['role'] === 'Senior') {
            Inertia::clearHistory();
            return redirect()->route('create.profile');
        }

        Inertia::clearHistory();
        return redirect()->route('create.profile.employer');
    }
}
