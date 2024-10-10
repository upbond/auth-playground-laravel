<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Auth0Service;

class HomeController extends Controller
{
    protected $auth0Service;

    public function __construct(Auth0Service $auth0Service)
    {
        $this->auth0Service = $auth0Service;
    }

    public function index()
    {
        // Check if the user is authenticated
        $decodedToken = $this->auth0Service->checkSession();

        if ($decodedToken) {
            // If the user is authenticated, redirect them to the profile page or show user info
            return view('profile', ['decodedToken' => $decodedToken]);
        } else {
            // If not authenticated, redirect to the login page
            return view('not_logged_in');
        }
    }
}
