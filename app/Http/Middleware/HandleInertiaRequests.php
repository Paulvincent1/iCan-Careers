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
            'auth.user' => fn () => $request->user()
            ? $request->user()->only('id', 'name', 'email')
            : null,
            'auth.worker_profile' => function () use($request) {
                if($request->user()){
                    if(!$request->user()->workerProfile){
                        return ['message' => 'Complete your profile now to let freelancers discover and connect with you!', 'route' => 'create.profile'];
                    }elseif(!$request->user()->workerSkills){

                        return ['message' => 'Add your skills to showcase your expertise and attract the right opportunities!', 'route' => 'add.skills'];
                    }
                }
                return null;
            }
        ]);
    }
}
