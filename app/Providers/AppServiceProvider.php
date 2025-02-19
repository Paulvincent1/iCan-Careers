<?php

namespace App\Providers;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
     
       // set this for production when you need https.
    //    $this->app['request']->server->set('HTTPS', true);
        //check that app is local
        if ($this->app->isLocal()) {
        //if local register your services you require for development
            // $this->app->register('Barryvdh\Debugbar\ServiceProvider');
        } else {
        //else register your services you require for production
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-applicants', function (User $user, JobPost $jobPost){
            return $user->id === $jobPost->employer_id;
        });
    }
}
