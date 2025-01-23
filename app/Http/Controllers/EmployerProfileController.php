<?php

namespace App\Http\Controllers;

use App\Models\EmployerProfile;
use Illuminate\Http\Request;

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
        return inertia('EmployerAccountSetup/CreateProfile');
    }

    public function storeProfile(Request $request){
        $fields = $request->validate([
            'full_name' => 'required|max:255',
            'phone_number' => 'required|max:255',
            'birth_year' => 'required|numeric|min:1900',
            'gender' => 'required',
        ]);
        
        EmployerProfile::create($fields);

        return redirect()->route('create.company.employer');
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
