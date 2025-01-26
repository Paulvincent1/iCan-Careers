<?php

namespace App\Http\Controllers;

use App\Models\BusinessInformation;
use App\Models\EmployerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            // return redirect()->
        }
        return inertia('EmployerAccountSetup/CreateProfile');
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
        
        $user->employerProfile()->create($fields);

        if($fields['employer_type'] === 'business'){
            $business = $request->validate([
                'business_name' => 'required|max:255',
                'business_logo' => 'required|image',
                'industry' => 'required',
                'business_description' => 'required',
                'business_location' =>'required' ,
            ]);

            $user->businessInformation()->create($business);
        }

        
        return redirect()->route('employer.dashboard');
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
