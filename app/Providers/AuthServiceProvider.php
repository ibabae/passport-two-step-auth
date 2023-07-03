<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));

        Auth::viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                // Extract the access token from the Authorization header
                $accessToken = str_replace('Bearer ', '', $request->header('Authorization'));

                // Find the user associated with the access token
                $user = User::where('api_token', $accessToken)->first();

                return $user;
            }
        });


    }
}
