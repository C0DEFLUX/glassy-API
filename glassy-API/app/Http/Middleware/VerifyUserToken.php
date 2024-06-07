<?php

namespace App\Http\Middleware;

use App\Models\Tokens;
use Closure;
use App\Models\User;

class VerifyUserToken
{
    public function handle($request, Closure $next)
    {
        // Get the user token from the request
        $userToken = $request->header('Authorization');

        // Find the token in the Token table
        $token = Tokens::where('token', $userToken)->first();

        // Check if the token exists
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the user associated with the token
//        $user = $token->id;

        $user = User::where('token_id', $token->id)->first();

        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Attach the user to the request for further use in the controller
        $request->merge(['authenticatedUser' => $user]);

        return $next($request);
    }
}
