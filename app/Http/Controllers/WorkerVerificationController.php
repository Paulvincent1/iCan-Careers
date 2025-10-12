<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkerVerification;
use App\Notifications\AdminWorkerVerificationNotification;
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
            'middle_name' => 'max:255',
            'last_name' => 'required|max:255',
            'suffix' => 'max:255',
            'id_image' => 'required|image',
            'selfie_image' => 'required|image',
        ]);
        // dd($fields['id_image']);

        // FOR FILE STORAGE
        // $idImage = Storage::disk('public')->put('images',$fields['id_image']);
        // $selfieImage = Storage::disk('public')->put('images',$fields['selfie_image']);


        // FOR CLOUDINARY
        $idImagePublicId = $request->file('id_image')->store('worker_verification', 'cloudinary');
        $idImageUrl = Storage::disk('cloudinary')->url($idImagePublicId);

        $selfieImagePublicId = $request->file('selfie_image')->store('worker_verification', 'cloudinary');
        $selfieImageUrl = Storage::disk('cloudinary')->url($idImagePublicId);


        // dd('/storage/'.$idImage);
        $user = Auth::user();
        $user->workerVerification()->create([
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'],
            'last_name' => $fields['last_name'],
            'suffix' =>  $fields['suffix'] ?? null,
            'id_image_public_id' => $idImagePublicId,
            'id_image_url' => $idImageUrl,
            'selfie_image_public_id' => $selfieImagePublicId,
            'selfie_image_url' => $selfieImageUrl,
        ]);

        $admin = User::whereHas('roles', function($query) {
            $query->where('name', 'Admin');
        })->first();

        $admin->notify(new AdminWorkerVerificationNotification(admin:$admin,user:$user));
        broadcast(new AdminWorkerVerificationNotification(admin:$admin,user:$user));



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
