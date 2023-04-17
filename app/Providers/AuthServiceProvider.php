<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */

    public function boot()
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function ($client, string $token) {
            $client = Client::where('email', $client->email)->first();
            if (!$client) {
                // Handle case when user is not found in Client model
                // You can throw an exception or return an error response
            }

            return 'http://127.0.0.1:8000/passwordReset?token='.$token.'&email='.$client->email;
        });
    }

    
}
