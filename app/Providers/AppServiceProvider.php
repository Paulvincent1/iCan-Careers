<?php

namespace App\Providers;

use App\Models\EmployerProfile;
use App\Models\JobPost;
use App\Models\User;
use App\Services\CloudinaryFileUploadService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{


    /**
     * Register any application services.
     */
    public function register(): void
    {

       // set this for production when you need https.
       $this->app['request']->server->set('HTTPS', true);
        //check that app is local
        if ($this->app->isLocal()) {
        //if local register your services you require for development
            // $this->app->register('Barryvdh\Debugbar\ServiceProvider');
        } else {
        //else register your services you require for production
            $this->app['request']->server->set('HTTPS', true);
        }

        $this->app->singleton(CloudinaryFileUploadService::class, function ($app) {
         return new CloudinaryFileUploadService(new Client());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config([
            'pdf.browsershot.options' => [
                'chromePath' => '/usr/bin/chromium',
                'args' => ['--no-sandbox'],
            ],
        ]);

        Gate::define('view-applicants', function (User $user, JobPost $jobPost){
            return $user->id === $jobPost->employer_id;
        });
        Gate::define('employer-profile-check', function (User $user){
            if(!$user->employerProfile) {
                return false;
            }

            return true;
        });

        Gate::define('worker-profile-check', function (User $user){
            if(!$user->workerProfile()) {
                return false;
            }

            return true;
        });

            // Share authenticated user globally with Inertia
        Inertia::share([
            'auth' => [
                'user' => function () {
                    $user = Auth::user();
                    return $user ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'profile_img' => $user->profile_img ?? '/assets/profile_placeholder.jpg', // Provide default image
                    ] : null;
                }
            ]
        ]);
    }

}
