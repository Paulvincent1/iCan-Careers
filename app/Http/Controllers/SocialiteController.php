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

    public function __construct(public InvoiceService $invoiceService)
    {

    }

    public function googleLogin(){
        return Socialite::driver('google')->redirect();
    }

    public function googleAuthentication(){
        $googleUser = Socialite::driver('google')->user();


        $user = User::where('google_id', $googleUser->id)->first();
        $emailUser = User::where('email', $googleUser->email)->first();

        if($user || $emailUser){
            Auth::login($user);


            $authUser = Auth::user();
            Inertia::clearHistory();

            if($authUser->roles()->first()){
                //TODO: return the page to normal after LOGIN
                $userRole = '';
                foreach($authUser->roles as $role){
                    $userRole = $role->name;
                }

                if($userRole === 'Senior' || $userRole === 'PWD'){
                    if(!$authUser->workerProfile){
                        return redirect()->route('create.profile');
                    }
                    return redirect()->route('worker.dashboard');
                }

                if($userRole === 'Employer'){
                    if(!$authUser->employerProfile){

                        return redirect()->route('create.profile.employer');
                    }

                    return redirect()->route('employer.dashboard');
                }

                if($userRole === 'Admin'){
                    return redirect()->route('admin.dashboard');
                }

            }else{
                // TODO: return the page to new view to choose whether employer or worker

                return redirect()->route('choose.role');

            }

        }else{

            $newUser = User::create(
                [
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Str::password(12),
                    'google_id'=> $googleUser->id,
                ]
            );

            Auth::login($newUser);
            Inertia::clearHistory();
            return redirect()->route('choose.role');

        }
    }

    public function chooseRole(Request $request) {
        if($request->user()->roles()->first()){

            return redirect()->back();
        }
        return inertia('Authentication/ChooseRole');
    }

   // TODO: do this controller p
    public function storeRole(Request $request){
        $fields = $request->validate(
            [
                'role' => 'required'
            ]
        );


        if($fields['role'] === 'Employer'){

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
                dd($fields['role']);
                //TODO: SHOW THIS MESSAGE IN CHOOSE ROLE
                return redirect()->back()->withErrors(['message' => 'Error creating account, Please try again.']);

            }


        }



        if($fields['role'] === 'PWD' || $fields['role'] === 'Senior'){

            $user = Auth::user();
            $role = Role::where('name', $fields['role'])->first();


            $user->roles()->attach($role->id);

            $user->balance()->create([
                'balance' => 0,
                'unsettlement' => 0,
            ]);
            $user->workerBasicInfo()->create();

        }

        if($fields['role'] === 'PWD' || $fields['role'] === 'Senior'){
            Inertia::clearHistory();
            return redirect()->route('create.profile');
        }

        Inertia::clearHistory();
        return redirect()->route('create.profile.employer');

    }
}
