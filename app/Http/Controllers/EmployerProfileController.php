<?php

namespace App\Http\Controllers;

use App\Models\BusinessInformation;
use App\Models\EmployerProfile;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class EmployerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function createProfile(){
        $user = Auth::user();
        if($user->employerProfile){
            return redirect()->route('employer.dashboard');
        }

        $businesses =  BusinessInformation::filter(request(['business_name']))->get();
        // dd($businesses);
        return inertia('EmployerAccountSetup/CreateProfile', ['bussinessProps' => $businesses]);
    }

 

    public function createCompanyInformation(){
        return inertia('EmployerAccountSetup/CompanyInformation');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function storeProfile(Request $request){
        // dd($request);
        $user = Auth::user();
        $fields = $request->validate([
            'full_name' => 'required|max:255',
            'phone_number' => 'required|numeric|min_digits:1',
            'birth_year' => 'required|numeric|min:1900',
            'gender' => 'required', 
            'employer_type' => 'required'
        ]);
        
        
        if($fields['employer_type'] === 'business'){

            if($request->business_id){
                $business =  BusinessInformation::where('id', $request->business_id)->first();
                
                if($business){

                    $user->employerProfile()->create([
                        'full_name' => $fields['full_name'],
                        'phone_number' => $fields['phone_number'],
                        'birth_year' => $fields['birth_year'],
                        'gender' => $fields['gender'],
                        'employer_type' => $fields['employer_type'],
                        'business_id' => $business->id,                       
                    ]);

                    return redirect()->route('employer.dashboard');
                }

                return redirect()->back()->withErrors(['business' => 'business found']);
            }
           
            $business = $request->validate([
                'business_name' => 'required|max:255',
                'business_logo' => 'required|image',
                'industry' => 'required',
                'business_description' => 'required',
                'business_location' =>'required',
            ]);
            // dd($request);    
  
            $logo = Storage::disk('public')->put('images', $request->business_logo);


           $businessInformation =  BusinessInformation::create([
                'business_name' => $business['business_name'],
                'business_logo' => '/storage/' . $logo,
                'industry' =>  $business['industry'],
                'business_description' =>  $business['business_description'],
                'business_location' =>  $business['business_location'],
                
            ]);


           $user->employerProfile()->create([
            'full_name' => $fields['full_name'],
            'phone_number' => $fields['phone_number'],
            'birth_year' => $fields['birth_year'],
            'gender' => $fields['gender'],
            'employer_type' => $fields['employer_type'],
            'business_id' => $businessInformation->id,
           ]);


            return redirect()->route('employer.dashboard');
        }

       
        $user->employerProfile()->create($fields);
        
        return redirect()->route('employer.dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployerProfile $employerProfile)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployerProfile $employerProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployerProfile $employerProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployerProfile $employerProfile)
    {
        //
    }
}
