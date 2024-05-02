<?php

namespace App\Http\Helpers;
// use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class Utils
{
    public static function makeRequest($url, $headers = [], $post_fields = null)
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

        // if ($post_fields !== null) {
        //     $response = Http::withHeaders($headers)
        //                     ->post($url, $post_fields);
        // } else {
        //     $response = Http::withHeaders($headers)
        //                     ->get($url);
        // }
    
        // return [$response->body(), $response->status() !== 200 ? $response->status() : null];
    }

    public static function getUserInfoUrl()
    {
        $account_id = getenv('AUTH0_ACCOUNT_ID') ?? '30abe0e1';
        $DOMAIN_AUTH = getenv('AUTH0_LOGIN3_DOMAIN') ?? "https://auth-wallet.dev.upbond.io";
        // Get OpenID configuration
        list($response, $error) = self::makeRequest("$DOMAIN_AUTH/.well-known/openid-configuration", [
            "x-account-id: $account_id"
        ]);
        if ($error) {
            echo "HTTP Error #:" . $error;
        } else {
            $response_data = json_decode($response, true);
            $userinfo_url = $response_data['userinfo_endpoint'];
            return $userinfo_url ?? $DOMAIN_AUTH . '/tokeninfo';
        }

        return $DOMAIN_AUTH . '/tokeninfo';
    }

    public static function getUserInfo($idToken)
    {
        $userinfo_url = self::getUserInfoUrl(); 

        if ($userinfo_url) {
            $user = null;

            // Get user info from userinfo endpoint
            list($userInfoResp, $error_userinfo) = self::makeRequest($userinfo_url, ["Authorization: Bearer $idToken"]);
            $userinfo = json_decode($userInfoResp, true);

            if ($error_userinfo) {
                echo "HTTP Error #:" . $error_userinfo;
            } else {
                if (isset($userinfo['errors']) && $userinfo['errors'][0] === "Not found") {
                    // Post user info if not found
                    self::makeRequest($userinfo_url, [
                        "Content-Type: application/json",
                        "Accept: application/json",
                        "Authorization: Bearer $idToken"
                    ], json_encode(['country' => 'japan']));

                    // Get user info again
                    list($userInfoResp, $error) = self::makeRequest($userinfo_url, ["Authorization: Bearer $idToken"]);
                    $user = json_decode($userInfoResp, true);
                } else {
                    $user = $userinfo;
                }
            }

            return $user;
        }

        return null;
    }

    public static function verifyJWT($token) {
        $jwt = $token;
        if (!is_string($token) && !is_a($token, 'Buffer')) {
            throw new InvalidArgumentException('invalid token input type');
        }

        // Split the JWT into its parts
        $parts = explode('.', $jwt);
        $length = count($parts);

        if ($length !== 3) {
            throw new InvalidArgumentException('invalid token input');
        }

        // Decode the payload and return
        $payload = base64_decode($parts[1]);
        return $payload;
    }
}
