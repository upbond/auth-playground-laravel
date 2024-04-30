<?php

use Auth0\Laravel\Facade\Auth0;
use Auth0\SDK\Configuration\{SdkConfiguration, SdkState};
use Illuminate\Support\Facades\Route;

Route::get('/private', function () {
  return response('Welcome! You are logged in.');
})->middleware('auth');

Route::get('/scope', function () {
  return response('You have `read:messages` permission, and can therefore access this resource.');
})->middleware('auth')->can('read:messages');

// Function to make a cURL request and handle errors
function makeCurlRequest($url, $headers = [], $post_fields = null)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if (!empty($headers)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }
  if ($post_fields !== null) {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
  }
  $response = curl_exec($ch);
  $error = curl_error($ch);
  curl_close($ch);
  return [$response, $error];
}

Route::get('/', function () {
  if (!auth()->check()) {
    return view('not_logged_in');
  }

  $sessionData = json_decode(session()->get('auth0_session'));
  $idToken = $sessionData->idToken;

  $account_id = getenv('AUTH0_ACCOUNT_ID') ?? '30abe0e1';
  $DOMAIN_AUTH = getenv('AUTH0_LOGIN3_DOMAIN') ?? "https://auth-wallet.dev.upbond.io";

  $userinfo_url = '';
  $user = null;
  // Get OpenID configuration
  list($response, $error) = makeCurlRequest("$DOMAIN_AUTH/.well-known/openid-configuration", [
    "Authorization: Bearer $idToken",
    "x-account-id: $account_id"
  ]);
  if ($error) {
    echo "cURL Error #:" . $error;
  } else {
    $response_data = json_decode($response, true);
    $userinfo_url = $response_data['userinfo_endpoint'];
    if ($userinfo_url) {
      // Get user info from userinfo endpoint
      list($userInfoResp, $error_userinfo) = makeCurlRequest($userinfo_url, ["Authorization: Bearer $idToken"]);
      $userinfo = json_decode($userInfoResp, true);

      if ($error_userinfo) {
        echo "cURL Error #:" . $error_userinfo;
      } else {
        if (isset($userinfo['errors']) && $userinfo['errors'][0] === "Not found") {
          // Post user info if not found
          list($response, $error) = makeCurlRequest($userinfo_url, [
              "Content-Type: application/json",
              "Accept: application/json",
              "Authorization: Bearer $idToken"
          ], json_encode(['age' => 23, 'gender' => 'male']));

          // Get user info again
          list($userInfoResp, $error) = makeCurlRequest($userinfo_url, ["Authorization: Bearer $idToken"]);
          $user = json_decode($userInfoResp, true);
      } else {
          $user = $userinfo;
      }
      }
    }
  }

  // - MOVE - USER GET FROM USERINFO API ON ABOVE FUNCTION
  // $user = auth()->user(); 

  $name = $user->name ?? 'User';
  $email = $user->email ?? '';

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
