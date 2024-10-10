<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Auth0Service;

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
        $idToken = $request->session()->get('id_token');
        $decodedToken = base64_decode($idToken);

        // Pass the decoded ID token data to the profile view
        return view('profile', ['decodedToken' => $decodedToken]);
    }
}
