<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Helpers\Utils;
use Exception;

class Userinfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $idToken = null;

        // Check if the idToken is present in the session data
        $sessionData = json_decode(session()->get('auth0_session'));
        if ($sessionData && isset($sessionData->idToken)) {
            $idToken = $sessionData->idToken;
        }

        // If idToken is not found in the session, check the Authorization header
        if (!$idToken && $request->header('Authorization')) {
            $authorizationHeader = $request->header('Authorization');
            $bearerToken = trim(str_replace('Bearer', '', $authorizationHeader));
            if ($bearerToken) {
                $idToken = $bearerToken;
            }
        }

        // If idToken is still not found, return a 401 Unauthorized response
        if (!$idToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // verify JWT
        try {
            Utils::verifyJWT($idToken);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        $user = Utils::getUserInfo($idToken);
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->merge(['user' => $user]);
        $request->merge(['token' => $idToken]);

        return $next($request);
    }
}
