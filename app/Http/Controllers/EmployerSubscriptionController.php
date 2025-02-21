<?php

namespace App\Http\Controllers;

use App\Models\EmployerSubscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployerSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    
    public function pricing()
    {
        return Inertia::render('Pricing/Pricing');
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
    public function show(EmployerSubscription $employerSubscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployerSubscription $employerSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployerSubscription $employerSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployerSubscription $employerSubscription)
    {
        //
    }
    
}
