<?php

namespace App\Http\Controllers;

use App\Models\BusinessInformation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;


class BusinessInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show($id)
    {
        $business = BusinessInformation::with(['employer', 'employers'])->findOrFail($id);

        // get the authenticated user
        $viewer = Auth::user();

        // get viewer role (check the roles relationship)
        $viewerRole = null;
        if ($viewer) {
            $viewerRole = $viewer->roles()->pluck('name')->first(); 
            // this assumes your Role model has a `name` field
        }

        return Inertia::render('BusinessInformation/BusinessInformation', [
            'business' => $business,
            'employer' => $business->employer,
            'employers' => $business->employers, // if you have many-to-many
            'viewerRole' => $viewerRole,     // send viewer role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessInformation $businessInformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessInformation $businessInformation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessInformation $businessInformation)
    {
        //
    }
}
