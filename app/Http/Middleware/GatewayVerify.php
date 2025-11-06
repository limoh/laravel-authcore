<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;

class GatewayVerify
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['msg' => 'missing token'], 401);
        }

        $client = new Client();
        $res = $client->post(config('services.authcore.introspect_url'), [
            'form_params' => ['token' => $token],
            'auth' => [config('services.authcore.client_id'), config('services.authcore.client_secret')]
        ]);

        $payload = json_decode($res->getBody(), true);
        if (!($payload['active'] ?? false)) {
            return response()->json(['msg' => 'invalid token'], 401);
        }

        $request->attributes->set('sso_user', $payload['user'] ?? null);
        return $next($request);
    }
}
