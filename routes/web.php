<?php

use Auth0\Laravel\Facade\Auth0;
use Auth0\SDK\Configuration\{SdkConfiguration, SdkState};
use Illuminate\Support\Facades\Route;
use App\Http\Helpers\Utils;
use App\Http\Controllers\Auth0Controller;
use App\Http\Controllers\ProfileController;

Route::get('/private', function () {
  return response('Welcome! You are logged in.');
})->middleware('auth');

Route::get('/scope', function () {
  return response('You have `read:messages` permission, and can therefore access this resource.');
})->middleware('auth')->can('read:messages');

Route::get('/', function () {
  if (!auth()->check()) {
    return view('not_logged_in');
  }

  $sessionData = json_decode(session()->get('auth0_session'));
  $idToken = $sessionData->accessToken;

  // $user = Utils::getUserInfo($idToken);
  // $name = isset($user['name']) ? $user['name'] : 'User';
  // $email = isset($user['email']) ? $user['email'] : '';

  // - MOVE - USER GET FROM USERINFO API ON ABOVE FUNCTION
  $user = auth()->user(); 
  $name = $user->name ?? 'User';
  $email = $user->email ?? '';

  return view('logged_in', ['name' => $name, 'email' => $email, 'user' => $user, 'token' => $idToken]);
});

Route::get('/login', [Auth0Controller::class, 'login'])->name('login');
Route::get('/callback', [Auth0Controller::class, 'callback'])->name('callback');
Route::post('/logout', [Auth0Controller::class, 'logout'])->name('logout');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');