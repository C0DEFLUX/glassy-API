<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class VerifyUserToken
{
    public function handle($request, Closure $next)
    {
        // Get the user token from the request
        $userToken = $request->header('Authorization');

        // Find the user by token in the database
        $user = User::where('token', $userToken)->first();

        // Check if the user with the given token exists
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Attach the user to the request for further use in the controller
        $request->merge(['authenticatedUser' => $user]);

        return $next($request);
    }
}
