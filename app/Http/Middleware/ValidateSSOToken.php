<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class ValidateSSOToken
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $token = $user?->token();

        if (!$token) {
            return response()->json(['message' => 'Missing token'], 401);
        }

        // Check DB revoked flag
        if ($token->revoked) {
            return response()->json(['message' => 'Token revoked'], 401);
        }

        // Check Redis blacklist
        if (Redis::exists("blacklist:token:{$token->id}")) {
            return response()->json(['message' => 'Token blacklisted'], 401);
        }

        return $next($request);
    }
}

