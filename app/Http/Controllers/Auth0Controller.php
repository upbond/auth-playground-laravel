<?php

namespace App\Http\Controllers;

use App\Services\Auth0Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Auth0Controller extends Controller
{
    protected $auth0Service;

    public function __construct(Auth0Service $auth0Service)
    {
        $this->auth0Service = $auth0Service;
    }

    public function login(Request $request)
    {
        $returnTo = $request->input('returnTo', '/');
        return redirect($this->auth0Service->login($returnTo));
    }

    public function callback(Request $request)
    {
        $accessToken = $this->auth0Service->callback();

        // Decode the base64 state parameter to get the returnTo value
        $state = $request->input('state');
        $returnTo = $state ? base64_decode($state) : '/';

        // Store the AccessToken in the session
        $request->session()->put('access_token', $accessToken);

        return redirect($returnTo);
    }

    public function logout()
    {
        return $this->auth0Service->logout();
    }
}
