<?php

namespace App\Http\Controllers;

use App\Models\BusinessInformation;
use App\Models\EmployerProfile;
use App\Models\EmployerSubscription;
use App\Models\JobPost;
use App\Models\User;
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

    public function createProfile()
    {
        $user = Auth::user();
        if ($user->employerProfile) {
            return redirect()->route('employer.dashboard');
        }

        $businesses =  BusinessInformation::filter(request(['business_name,business_logo']))->get();
        // dd($businesses);
        return inertia('EmployerAccountSetup/CreateProfile', ['bussinessProps' => $businesses]);
    }



    public function createCompanyInformation()
    {
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
     */ // this function is not used.
    public function show(User $applicantId)
    {

        $employerProfile = $applicantId->employerProfile;

        return inertia('Employer/Profile', [
            'userProp' => $applicantId,
            'employerProfileProp' => $employerProfile,
            'messageProp' => session('message'),
            'visitor' => true
        ]);
    }



    public function myProfile(Request $request)
    {
        if (!Gate::allows('employer-profile-check')) {
            return redirect()->route('create.profile.employer');
        }

        $user = Auth::user();
        $employerProfile = $user->employerProfile;
        $jobsPosted = JobPost::with('employer.businessInformation')
            ->where('employer_id', $user->id)
            ->get();

        $business = $user->employerProfile?->businessInformation;
        $subscription = EmployerSubscription::where('employer_id', $user->id)->first();

        $recentReview = $user->receivedReviews()->with('reviewer')->orderBy('created_at', 'desc')->first();

        $sumStars = 0;
        $reviewCount = $user->receivedReviews()->get()->count();
        $user->receivedReviews()->get()->pluck('star')
            ->each(
                function ($e) use (&$sumStars) {
                    $sumStars += $e;
                }
            );

        if ($sumStars) {
            $average = $sumStars / $reviewCount;

            $roundedAverageStar = round($average, 2);
        }


        return inertia('Employer/Profile', [
            "user" => $user,
            'employerProfileProp' => $employerProfile,
            'businessProps' => $business ?? null,
            'messageProp' => session('message'),
            'jobsPostedProps' => $jobsPosted, // Pass multiple jobs
            'subscriptionProps' => $subscription,
            'recentReview' => $recentReview,
            'averageStar' => $roundedAverageStar ?? 0.00
        ]);
    }

    public function showEmployerProfile(User $id)
    {
        if (!$id->employerProfile) {
            return redirect()->back();
        }

        $employerProfile = $id->employerProfile;
        $jobsPosted = JobPost::with('employer.businessInformation')
            ->where('employer_id', $id->id)
            ->get();;
        $business = $id->employerProfile?->businessInformation;
        $subscription = EmployerSubscription::where('employer_id', $id->id)->first();


        $recentReview = $id->receivedReviews()->with('reviewer')->orderBy('created_at', 'desc')->first();

        $sumStars = 0;
        $reviewCount = $id->receivedReviews()->get()->count();
        $id->receivedReviews()->get()->pluck('star')
            ->each(
                function ($e) use (&$sumStars) {
                    $sumStars += $e;
                }
            );


        if ($sumStars) {
            $average = $sumStars / $reviewCount;

            $roundedAverageStar = round($average, 2);
        }


        return inertia('Employer/Profile', [
            "user" => $id,
            'employerProfileProp' => $employerProfile,
            'businessProps' => $business ?? null,
            'messageProp' => session('message'),
            'jobsPostedProps' => $jobsPosted, // Pass multiple jobs
            'subscriptionProps' => $subscription,
            'visitor' => true,
            'recentReview' => $recentReview,
            'averageStar' => $roundedAverageStar ?? 0.00
        ]);
    }



    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employerProfile = $user->employerProfile;

        if (!$employerProfile) {
            return redirect()->back()->with([
                'message' => 'Employer profile not found.',
                'messageType' => 'error'
            ]);
        }

        // ========== Scalar field validation & update ==========
        $request->validate([
            'full_name'    => 'nullable|string|min:1',
            'birth_year'   => 'nullable|integer|min:1900|max:' . now()->year,
            'gender'       => 'nullable|string|in:Male,Female,Other',
            'phone_number' => 'nullable|string',
        ]);

        $employerProfile->update([
            'full_name'    => $request->full_name ?? $employerProfile->full_name,
            'birth_year'   => $request->birth_year ?? $employerProfile->birth_year,
            'gender'       => $request->gender ?? $employerProfile->gender,
            'phone_number' => $request->phone_number ?? $employerProfile->phone_number,
        ]);

        // ========== Profile Image (Cloudinary) ==========
        if ($request->hasFile('profile_img')) {
            $request->validate([
                'profile_img' => [
                    'required',
                    'image',
                    'mimes:jpeg,jpg,png,webp',   // no GIFs
                    'max:2048',                  // 2MB
                ]
            ]);

            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $mime = $request->file('profile_img')->getMimeType();
            if (!in_array($mime, $allowedMimes, true)) {
                return back()->withErrors([
                    'profile_img' => 'Profile photo must be JPEG, JPG, PNG, or WEBP. GIF/video files are not allowed.',
                ])->withInput();
            }

            // delete old from Cloudinary if present
            if ($user->profile_img_public_id && Storage::disk('cloudinary')->exists($user->profile_img_public_id)) {
                Storage::disk('cloudinary')->delete($user->profile_img_public_id);
            }

            // upload new
            $publicId = Storage::disk('cloudinary')->putFile('employers/profile', $request->file('profile_img'));
            $url      = Storage::disk('cloudinary')->url($publicId);

            $user->update([
                'profile_img_public_id' => $publicId,
                'profile_img_url'       => $url,
            ]);
        }

        // ========== Cover Photo (Cloudinary) ==========
        if ($request->hasFile('cover_photo')) {
            $request->validate([
                'cover_photo' => [
                    'required',
                    'image',
                    'mimes:jpeg,jpg,png,webp', // no GIFs
                    'max:5120',                // 5MB
                ]
            ]);

            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $mime = $request->file('cover_photo')->getMimeType();
            if (!in_array($mime, $allowedMimes, true)) {
                return back()->withErrors([
                    'cover_photo' => 'Cover photo must be JPEG, JPG, PNG, or WEBP. GIF/video files are not allowed.',
                ])->withInput();
            }

            // delete old from Cloudinary if present
            if ($user->cover_photo_public_id && Storage::disk('cloudinary')->exists($user->cover_photo_public_id)) {
                Storage::disk('cloudinary')->delete($user->cover_photo_public_id);
            }

            // upload new
            $publicId = Storage::disk('cloudinary')->putFile('employers/cover', $request->file('cover_photo'));
            $url      = Storage::disk('cloudinary')->url($publicId);

            $user->update([
                'cover_photo_public_id' => $publicId,
                'cover_photo_url'       => $url,
            ]);
        }

        return redirect()->back()->with([
            'message' => 'Profile updated successfully!',
            'messageType' => 'success'
        ]);
    }



    public function storeProfile(Request $request)
    {
        $user = Auth::user();
        $fields = $request->validate([
            'full_name' => 'required|max:255',
            'phone_number' => 'required|numeric|min_digits:1',
            'birth_year' => 'required|numeric|min:1900',
            'gender' => 'required',
            'employer_type' => 'required'
        ]);

        if ($fields['employer_type'] === 'business') {
            if ($request->business_id) {
                $business = BusinessInformation::where('id', $request->business_id)->first();

                if ($business) {
                    $business->employers()->syncWithoutDetaching([$user->id]);

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

                return redirect()->back()->withErrors(['business' => 'Business not found']);
            }

            // ✅ FIXED: Use 'business_logo' for validation (matches Vue form field)
            $business = $request->validate([
                'business_name' => 'required|max:255',
                'business_logo' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120', // ✅ Changed from business_logo_url
                'industry' => 'required',
                'business_description' => 'required',
                'business_location' => 'required',
            ]);

            // Upload to Cloudinary - ✅ This stays the same
            $publicId = Storage::disk('cloudinary')->putFile('businesses/logo', $request->file('business_logo'));
            $url = Storage::disk('cloudinary')->url($publicId);

            $businessInformation = BusinessInformation::create([
                'user_id' => $user->id,
                'business_name' => $business['business_name'],
                'business_logo_public_id' => $publicId,
                'business_logo_url' => $url, // ✅ Store the URL in database
                'industry' => $business['industry'],
                'business_description' => $business['business_description'],
                'business_location' => $business['business_location'],
            ]);

            $businessInformation->employers()->attach($user->id);

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

    public function editProfile()
    {
        $user = Auth::user();
        $profile = $user->employerProfile()->with('businessInformation')->first();
        $businesses = BusinessInformation::all();

        return inertia('EmployerAccountSetup/CreateProfile', [
            'bussinessProps' => $businesses,
            'profile' => $profile, // Pass current profile
        ]);
    }

    public function updateProfileFull(Request $request)
    {
        $user = Auth::user();
        $profile = $user->employerProfile()->with('businessInformation')->first();

        if (!$profile) {
            return redirect()->route('create.profile.employer');
        }

        $fields = $request->validate([
            'full_name' => 'required|max:255',
            'phone_number' => 'required|numeric|min_digits:1',
            'birth_year' => 'required|numeric|min:1900',
            'gender' => 'required',
            'employer_type' => 'required',
            'business_id' => 'nullable|integer|exists:business_information,id',
            'business_name' => 'nullable|max:255',
            'business_logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'industry' => 'nullable|string',
            'business_description' => 'nullable|string',
            'business_location' => 'nullable',
        ]);

        if ($fields['employer_type'] === 'business') {
            if ($request->business_id) {
                $business = BusinessInformation::find($request->business_id);
                $business->employers()->syncWithoutDetaching([$user->id]);

                $profile->update([
                    ...$fields,
                    'business_id' => $request->business_id,
                ]);
                return back()->with('message', 'Profile updated with existing business!');
            }

            // Create new business with Cloudinary
            $publicId = null;
            $url = null;

            if ($request->hasFile('business_logo')) {
                $publicId = Storage::disk('cloudinary')->putFile('businesses/logo', $request->file('business_logo'));
                $url = Storage::disk('cloudinary')->url($publicId);
            }

            $businessInformation = BusinessInformation::create([
                'user_id' => $user->id,
                'business_name' => $fields['business_name'],
                'business_logo_public_id' => $publicId,
                'business_logo_url' => $url,
                'industry' => $fields['industry'],
                'business_description' => $fields['business_description'],
                'business_location' => $fields['business_location'],
            ]);

            $businessInformation->employers()->syncWithoutDetaching([$user->id]);

            $profile->update([
                ...$fields,
                'business_id' => $businessInformation->id,
            ]);

            return back()->with('message', 'Profile updated with new business!');
        }

        // Individual
        $profile->update([
            ...$fields,
            'business_id' => null,
        ]);

        return back()->with('message', 'Profile updated as individual!');
    }




    public function edit()
    {
        $user = Auth::user();
        $employerProfile = $user->employerProfile;

        if (!$employerProfile) {
            return redirect()->route('create.profile.employer');
        }

        $businesses = BusinessInformation::filter(request(['business_name']))->get();
        $currentBusiness = $employerProfile->businessInformation;

        return inertia('EmployerAccountSetup/EditProfile', [
            'employerProfile' => $employerProfile,
            'businesses' => $businesses,
            'currentBusiness' => $currentBusiness,
            'user' => $user
        ]);
    }



    public function update(Request $request)
    {
        $user = Auth::user();
        $employerProfile = $user->employerProfile;

        if (!$employerProfile) {
            return redirect()->route('create.profile.employer');
        }

        $fields = $request->validate([
            'full_name' => 'required|max:255',
            'phone_number' => 'required|numeric|min_digits:1',
            'birth_year' => 'required|numeric|min:1900',
            'gender' => 'required',
            'employer_type' => 'required'
        ]);

        if ($fields['employer_type'] === 'business') {
            $request->validate([
                'business_id' => 'required_without:business_name|nullable|exists:business_information,id',
                'business_name' => 'required_without:business_id|max:255',
                'business_logo' => 'required_without:business_id|image|mimes:jpeg,jpg,png,webp|max:5120|nullable',
                'industry' => 'required_without:business_id',
                'business_description' => 'required_without:business_id',
                'business_location' => 'required_without:business_id'
            ]);

            if ($request->business_id) {
                $business = BusinessInformation::findOrFail($request->business_id);

                if (!$business->employers->contains($user->id)) {
                    $business->employers()->attach($user->id);
                }

                $fields['business_id'] = $business->id;
            } else {
                $businessFields = $request->validate([
                    'business_name' => 'required|max:255',
                    'business_logo' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
                    'industry' => 'required',
                    'business_description' => 'required',
                    'business_location' => 'required'
                ]);

                // Upload to Cloudinary
                $publicId = Storage::disk('cloudinary')->putFile('businesses/logo', $request->file('business_logo'));
                $url = Storage::disk('cloudinary')->url($publicId);

                $business = BusinessInformation::create([
                    'user_id' => $user->id,
                    'business_name' => $businessFields['business_name'],
                    'business_logo_public_id' => $publicId,
                    'business_logo_url' => $url,
                    'industry' => $businessFields['industry'],
                    'business_description' => $businessFields['business_description'],
                    'business_location' => $businessFields['business_location'],
                ]);

                $business->employers()->attach($user->id);
                $fields['business_id'] = $business->id;
            }
        } else {
            $fields['business_id'] = null;
        }

        $employerProfile->update($fields);

        return redirect()->route('employer.profile')->with('message', 'Profile updated successfully!');
    }

    public function updateBusiness(Request $request)
    {
        $user = Auth::user();
        $employerProfile = $user->employerProfile;

        if (!$employerProfile || $employerProfile->employer_type !== 'business') {
            return redirect()->back()->withErrors(['message' => 'You are not a business employer']);
        }

        $business = $employerProfile->businessInformation;

        if (!$business) {
            return redirect()->back()->withErrors(['message' => 'Business information not found']);
        }

        $fields = $request->validate([
            'business_name' => 'required|max:255',
            'industry' => 'required',
            'business_description' => 'required',
            'business_location' => 'required'
        ]);

        // Handle logo update if provided
        if ($request->hasFile('business_logo')) {
            $request->validate([
                'business_logo' => 'image|mimes:jpeg,jpg,png,webp|max:5120'
            ]);

            // Delete old logo from Cloudinary if exists
            if ($business->business_logo_public_id && Storage::disk('cloudinary')->exists($business->business_logo_public_id)) {
                Storage::disk('cloudinary')->delete($business->business_logo_public_id);
            }

            // Upload new logo to Cloudinary
            $publicId = Storage::disk('cloudinary')->putFile('businesses/logo', $request->file('business_logo'));
            $url = Storage::disk('cloudinary')->url($publicId);

            $fields['business_logo_public_id'] = $publicId;
            $fields['business_logo_url'] = $url;
        }

        $business->update($fields);

        return redirect()->back()->with('message', 'Business information updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployerProfile $employerProfile)
    {
        //
    }
}
