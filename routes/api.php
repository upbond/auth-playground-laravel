<?php

use Auth0\Laravel\Facade\Auth0;
use Auth0\SDK\Configuration\{SdkConfiguration, SdkState};
use Illuminate\Support\Facades\Route;
use App\Http\Helpers\Utils;
use Illuminate\Support\Arr;

Route::get('/private', function () {
  return response()->json([
    'message' => 'Your token is valid; you are authorized.',
  ]);
})->middleware('auth');

Route::get('/scope', function () {
  return response()->json([
    'message' => 'Your token is valid and has the `read:messages` permission; you are authorized.',
  ]);
})->middleware('auth')->can('read:messages');

Route::get('/', function () {
  if (!auth()->check()) {
    return response()->json([
      'message' => 'You did not provide a valid token.',
    ]);
  }

  return response()->json([
    'message' => 'Your token is valid; you are authorized.',
    'id' => auth()->id(),
    'token' => auth()?->user()?->getAttributes(),
  ]);
});

Route::get('/me', function () {
  $user = auth()->id();
  $profile = cache()->get($user);

  if (null === $profile) {
    $endpoint = Auth0::management()->users();
    $profile = $endpoint->get($user);
    $profile = Auth0::json($profile);

    cache()->put($user, $profile, 120);
  }

  $name = $profile['name'] ?? 'Unknown';
  $email = $profile['email'] ?? 'Unknown';

  return response()->json([
    'name' => $name,
    'email' => $email,
  ]);
})->middleware('auth');

Route::get('/userinfo', function (Illuminate\Http\Request $request) {
  $user = $request->input('user');
  return response()->json([
    'message' => 'Hi ' . $user['name'] ?? 'User',
    'user' => $user
  ], 200);
})->middleware('userinfo');

Route::post('/userinfo', function (Illuminate\Http\Request $request) {
  $token = $request->input('token');
  if ($request->getContent()) {
    $userinfo_url = Utils::getUserInfoUrl(); 
    $body = $request->all();
    $body = Arr::except($body, ['token', 'user']);
  
    list($response, $error) = Utils::makeRequest($userinfo_url, [
      "Content-Type: application/json",
      "Accept: application/json",
      "Authorization: Bearer $token"
    ], json_encode($body));
    
    if ($error) {
      return response()->json(['error' => $error], 400);
    }
  }

  $user = Utils::getUserInfo($token);

  return response()->json([
    'message' => 'Success to Update Data',
    'user' => $user
  ], 200);
})->middleware('userinfo');
