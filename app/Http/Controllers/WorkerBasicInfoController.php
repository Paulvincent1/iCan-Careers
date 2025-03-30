<?php

namespace App\Http\Controllers;

use App\Models\WorkerBasicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerBasicInfoController extends Controller
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
    public function show(WorkerBasicInfo $workerBasicInfo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkerBasicInfo $workerBasicInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkerBasicInfo $workerBasicInfo)
    {

        // dd($request);
        $user = Auth::user();
        if($request->input('address')){

            $user->workerBasicInfo()->update([
                'address' => $request->address
            ]);
        }

        if($request->input('website_link')){

            $user->workerBasicInfo()->update([
                'website_link' => $request->website_link
            ]);
        }

        return redirect()->back()->with('message', 'Sucessfully updated!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkerBasicInfo $workerBasicInfo)
    {
        //
    }
}
