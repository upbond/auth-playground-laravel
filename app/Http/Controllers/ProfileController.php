<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Auth0Service;
use Illuminate\Support\Facades\Log;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use CoderCat\JWKToPEM\JWKConverter;

function jwkToPem(array $jwk)
{
    // Create a JWK object from the array
    $jwkConverter = new JWKConverter();
    Log::info('jwk', ['jwk' => $jwk]);

    // Export the JWK to PEM format
    $pem = $jwkConverter->toPEM($jwk);

    return $pem;
}

function decodeIdTokenWithJWKS($idToken)
{
    $auth0Domain = env('AUTH0_DOMAIN'); // e.g., 'https://auth-wallet.stg.upbond.io/'
    $jwksUrl = "{$auth0Domain}.well-known/jwks.json";

    try {
        // Fetch the JWKS from the Auth0 domain
        $client = new Client();
        $response = $client->get($jwksUrl);
        $jwks = json_decode($response->getBody()->getContents(), true);
        Log::info('jwks', ['jwks' => $jwks]);

        // Decode the JWT header to get the 'kid' (key ID)
        $tokenParts = explode('.', $idToken);
        $header = json_decode(base64_decode($tokenParts[0]), true);
        $kid = $header['kid'];

        // Find the key with the same 'kid'
        $publicKey = null;
        foreach ($jwks['keys'] as $key) {
            if ($key['kid'] === $kid) {
                $publicKey = jwkToPem($key);
                break;
            }
        }

        // Use the PEM key to verify the ID token
        $decoded = JWT::decode($idToken, new Key($publicKey, 'RS256'));
        Log::info('decoded', ['decoded' => $decoded]);

        // Verify other claims like 'iss' and 'aud'
        if ($decoded->iss !== $auth0Domain) {
            throw new \Exception('Invalid issuer.');
        }

        if (!in_array(env('AUTH0_CLIENT_ID'), (array) $decoded->aud)) {
            throw new \Exception('Invalid audience.');
        }

        return $decoded;
    } catch (\Exception $e) {
        \Log::error('Failed to decode ID token with JWKS:', ['error' => $e->getMessage()]);
        return null;
    }
}


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
        $decodedToken = decodeIdTokenWithJWKS($idToken);

        Log::info('User has logged in.', ['idToken' => $idToken]);
        Log::info('User has logged in.', ['decodedToken' => $decodedToken]);

        // Pass the decoded ID token data to the profile view
        return view('profile', ['decodedToken' => $decodedToken]);
    }
}
