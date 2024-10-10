<?php

namespace App\Services;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Support\Facades\Log;

class Auth0Service
{
    protected $auth0;

    public function __construct()
    {
        $this->auth0 = new Auth0(new SdkConfiguration([
            'domain' => env('AUTH0_DOMAIN'),
            'clientId' => env('AUTH0_CLIENT_ID'),
            'clientSecret' => env('AUTH0_CLIENT_SECRET'),
            'redirectUri' => env('AUTH0_REDIRECT_URI'),
            'cookieSecret' => env('APP_KEY'),
        ]));
    }

    public function login($returnTo = '/')
    {
        return $this->auth0->login(null, [
            'redirect_uri' => route('callback'),
            'scope' => 'openid profile email',            
            'state' => base64_encode($returnTo),
        ]);
    }

    public function callback()
    {
        $user = $this->auth0->exchange();
        Log::info('User has logged in.', ['user' => $user]);
        return $user;
    }

    public function logout()
    {
        $this->auth0->logout();
    }
}
