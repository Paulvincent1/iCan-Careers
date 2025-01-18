<?php

namespace App\Http\Controllers;

use App\Models\WorkerVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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
        $user = Auth::user();
        if($user->workerVerification){
            return redirect()->route('worker.dashboard');
        }
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
        // dd($fields);

        $idImage = Storage::disk('public')->put('images',$fields['id_image']);
        $selfieImage = Storage::disk('public')->put('images',$fields['selfie_image']);

        // dd('/storage/'.$idImage);
        $user = Auth::user();
        $user->workerVerification()->create([
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'],
            'last_name' => $fields['last_name'],
            'suffix' =>  $fields['suffix'],
            'id_image' =>  '/storage/'.$idImage,
            'selfie_image' => '/storage/'.$selfieImage,
        ]);

        Inertia::clearHistory();
        return redirect()->route('worker.dashboard');
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
