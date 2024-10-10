<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Auth0Service;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected $auth0Service;

    public function __construct(Auth0Service $auth0Service)
    {
        $this->auth0Service = $auth0Service;
    }

    /**
     * Show the decoded ID token content.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Request $request)
    {
        // Get the decoded ID token
        $accessToken = $request->session()->get('access_token');
        $decodedToken = $this->auth0Service->decodeAccessTokenWithJWKS($accessToken);
        // Pass the decoded ID token data to the profile view
        return view('profile', ['decodedToken' => $decodedToken]);
    }
}
