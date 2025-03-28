<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth.user.authenticated' => fn () => $request->user()
            ? $request->user()->only('id', 'name', 'email', 'profile_img','verified')
            : null,
            'auth.worker_profile' => function () use($request) {
                if($request->user()){
                    if($request->user()->roles()->whereIn('name', ['PWD', 'Senior'])->exists()){
                        if(!$request->user()->workerProfile){
                            return ['message' => 'Complete your profile now to let freelancers discover and connect with you!', 'route' => 'create.profile'];
                        }elseif(!$request->user()->workerSkills()->first()){
                            return ['message' => 'Add your skills to showcase your expertise and attract the right opportunities!', 'route' => 'add.skills'];
                        }
                    }
                }
                return null;
            },
            'auth.worker_verified' => function () use($request){
                return $request->user() ? ($request->user()->workerVerification ? $request->user()->workerVerification : null) : null;
            },
            'auth.wokerIsVerified' => fn ()  =>  $request->user()?->verified,
            'auth.employer_profile' => function () use($request) {
                if($request->user()){
                    if($request->user()->roles()->where('name','Employer')->exists()){
                        if(!$request->user()->employerProfile) {          
                            return ['message' => 'Complete your profile now to post jobs.', 'route' => 'create.profile.employer'];
                        }
                    }

                }
            },
            'auth.user.employer.subscription' => fn () => $request->user()?->employerSubscription,
            'auth.user.role' => fn () => $request->user()?->roles()->first(),
            'auth.user.unreadNotifications' => fn () => $request->user()?->unreadNotifications,
            'csrf_token' => fn() => $request->session()->token(),
        ]);
    }
}
