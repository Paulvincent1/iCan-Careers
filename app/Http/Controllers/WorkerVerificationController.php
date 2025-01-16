<?php

namespace App\Http\Controllers;

use App\Models\WorkerVerification;
use Illuminate\Http\Request;

class WorkerVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('WorkerAccountSetup/Verification/Verification');
    }

    public function verificationId(){
        return inertia('WorkerAccountSetup/Verification/IdVerfication');
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
       
        $fields = $request->validate([
            'first_name' => 'required|max:255',
            'middle_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'suffix' => 'required|max:255',
            'id_image' => 'required|image',
            'selfie_image' => 'required|image',
        ]);
        dd($fields);
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkerVerification $workerVerification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkerVerification $workerVerification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkerVerification $workerVerification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkerVerification $workerVerification)
    {
        //
    }
}
