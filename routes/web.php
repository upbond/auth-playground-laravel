<?php

use Auth0\Laravel\Facade\Auth0;
use Auth0\SDK\Configuration\{SdkConfiguration, SdkState};
use Illuminate\Support\Facades\Route;
use App\Http\Helpers\Utils;

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
  $idToken = $sessionData->idToken;

  $user = Utils::getUserInfo($idToken);

  $name = isset($user['name']) ? $user['name'] : 'User';
  $email = isset($user['email']) ? $user['email'] : '';

  // - MOVE - USER GET FROM USERINFO API ON ABOVE FUNCTION
  // $user = auth()->user(); 
  // $name = $user->name ?? 'User';
  // $email = $user->email ?? '';

  return view('logged_in', ['name' => $name, 'email' => $email, 'user' => $user, 'token' => $idToken]);
});

Route::get('/colors', function () {
  $endpoint = Auth0::management()->users();

  $colors = ['red', 'blue', 'green', 'black', 'white', 'yellow', 'purple', 'orange', 'pink', 'brown'];

  $endpoint->update(
    id: auth()->id(),
    body: [
      'user_metadata' => [
        'color' => $colors[random_int(0, count($colors) - 1)]
      ]
    ]
  );

  $metadata = $endpoint->get(auth()->id()); // Retrieve the user's metadata.
  $metadata = Auth0::json($metadata); // Convert the JSON to a PHP array.

  $color = $metadata['user_metadata']['color'] ?? 'unknown';
  $name = auth()->user()->name;

  return response("Hello {$name}! Your favorite color is {$color}.");
})->middleware('auth');
