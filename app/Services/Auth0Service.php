<?php

namespace App\Services;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use CoderCat\JWKToPEM\JWKConverter;

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

    private function jwkToPem(array $jwk)
    {
        // Create a JWK object from the array
        $jwkConverter = new JWKConverter();
        Log::info('jwk', ['jwk' => $jwk]);
    
        // Export the JWK to PEM format
        $pem = $jwkConverter->toPEM($jwk);
    
        return $pem;
    }
    
    public function decodeAccessTokenWithJWKS($accessToken)
    {
        $auth0Domain = env('AUTH0_DOMAIN'); // e.g., 'https://auth-wallet.stg.upbond.io/'
        $jwksUrl = "https://{$auth0Domain}/.well-known/jwks.json";
        $issuer = "https://{$auth0Domain}/";
    
        try {
            // Fetch the JWKS from the Auth0 domain
            $client = new Client();
            $response = $client->get($jwksUrl);
            $jwks = json_decode($response->getBody()->getContents(), true);
            Log::info('jwks', ['jwks' => $jwks]);
    
            // Decode the JWT header to get the 'kid' (key ID)
            $tokenParts = explode('.', $accessToken);
            $header = json_decode(base64_decode($tokenParts[0]), true);
            $kid = $header['kid'];
    
            // Find the key with the same 'kid'
            $publicKey = null;
            foreach ($jwks['keys'] as $key) {
                if ($key['kid'] === $kid) {
                    $publicKey = $this->jwkToPem($key);
                    break;
                }
            }
    
            // Use the PEM key to verify the ID token
            $decoded = JWT::decode($accessToken, new Key($publicKey, 'RS256'));
            Log::info('decoded', ['decoded' => $decoded]);
    
            // Verify other claims like 'iss' and 'aud'
            if ($decoded->iss !== $issuer) {
                throw new \Exception('Invalid issuer.');
            }
    
            return $decoded;
        } catch (\Exception $e) {
            \Log::error('Failed to decode ID token with JWKS:', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function checkSession()
    {
        // Retrieve the ID token from the session
        $accessToken = Session::get('access_token');

        // If there's no ID token, the user is not authenticated
        if (!$accessToken) {
            return null;
        }

        // Decode and validate the ID token (you can use the decodeAccessTokenWithJWKS method)
        try {
            // Assuming RS256 is used and we have a method to get the correct public key
            $decodedToken = $this->decodeAccessTokenWithJWKS($accessToken);

            // You can return the decoded token or user data here
            return $decodedToken;
        } catch (\Exception $e) {
            \Log::error('Failed to validate session ID token:', ['error' => $e->getMessage()]);
            return null;
        }
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

        // Retrieve the tokens after a successful exchange
        $credentials = $this->auth0->getCredentials();
        $accessToken = $credentials->accessToken ?? null;

        return $accessToken;
    }

    public function logout()
    {
        // Clear the session data
        Session::forget('access_token');
        Session::flush();   
        // Optionally, you can also log out of Auth0
        $logoutUrl = 'https://' . env('AUTH0_DOMAIN') . '/v2/logout?client_id=' . env('AUTH0_CLIENT_ID') . '&returnTo=' . urlencode(env('APP_URL'));                     
        return redirect($logoutUrl);
    }
}
