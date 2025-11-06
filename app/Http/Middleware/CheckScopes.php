<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckScopes
{
    /**
     * Handle an incoming request.
     *
     * Usage example:
     * Route::get('/user', [UserController::class, 'index'])->middleware('scopes:read-user,view-profile');
     */
    public function handle(Request $request, Closure $next, ...$scopes)
    {
        $user = $request->user();

        // Ensure there's a valid token
        $token = $user?->token();
        if (!$token) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // Get token scopes from Passport
        $tokenScopes = $token->scopes ?? [];

        // Check if all required scopes are present
        foreach ($scopes as $scope) {
            if (!in_array($scope, $tokenScopes)) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Insufficient scope: ' . $scope,
                ], 403);
            }
        }

        return $next($request);
    }
}
