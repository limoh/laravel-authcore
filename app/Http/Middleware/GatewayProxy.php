<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class GatewayProxy
{
    /**
     * Handle an incoming request and proxy it to a downstream service.
     */
    public function handle(Request $request, Closure $next, $serviceBaseUrl)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return response()->json(['msg' => 'missing_token'], 401);
        }

        // Step 1: Validate / introspect token
        $payload = $this->introspectToken($bearer);
        if (!$payload || !($payload['active'] ?? false)) {
            return response()->json(['msg' => 'invalid_token'], 401);
        }

        // Step 2: Generate internal token (short-lived JWT)
        $internalToken = $this->makeInternalToken($payload);

        // Step 3: Forward request to downstream service
        try {
            $forward = $this->forwardRequest($request, $serviceBaseUrl, $internalToken);
        } catch (\Exception $e) {
            Log::error('Gateway proxy forwarding failed', ['error' => $e->getMessage()]);
            return response()->json(['msg' => 'service_unavailable'], 502);
        }

        // Step 4: Stream response back to client
        $headers = collect($forward->getHeaders())
            ->map(fn($values) => implode(', ', $values))
            ->toArray();

        return response($forward->getBody()->getContents(), $forward->getStatusCode())
            ->withHeaders($headers);
    }

    /**
     * Introspect the bearer token with the AuthCore service.
     */
    protected function introspectToken(string $token): ?array
    {
        try {
            $client = new Client(['timeout' => 5]);
            $res = $client->post(config('services.authcore.introspect_url'), [
                'form_params' => ['token' => $token],
                'auth' => [
                    config('services.authcore.client_id'),
                    config('services.authcore.client_secret'),
                ],
            ]);
            return json_decode($res->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Token introspection failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a short-lived internal JWT token for internal service calls.
     */
    protected function makeInternalToken(array $payload): string
    {
        $claims = [
            'iss' => config('app.url'),
            'sub' => $payload['user']['id'] ?? null,
            'scp' => $payload['scope'] ?? [],
            'iat' => time(),
            'exp' => time() + 60, // 1-minute lifetime
        ];

        $privateKeyPath = storage_path('app/internal.key');
        if (!file_exists($privateKeyPath)) {
            throw new \Exception('Internal signing key not found.');
        }

        $privateKey = file_get_contents($privateKeyPath);
        return JWT::encode($claims, $privateKey, 'RS256');
    }

    /**
     * Forward the original request to the downstream service.
     */
    protected function forwardRequest(Request $request, string $baseUrl, string $internalToken)
    {
        $client = new Client([
            'base_uri' => rtrim($baseUrl, '/') . '/',
            'timeout' => 10,
        ]);

        // Flatten Laravel headers
        $headers = collect($request->headers->all())
            ->map(fn($values) => implode(', ', $values))
            ->toArray();

        // Add proxy headers
        $headers['Authorization'] = 'Bearer ' . $internalToken;
        $headers['X-Forwarded-For'] = $request->ip();
        $headers['X-Original-Authorization'] = 'Bearer ' . $request->bearerToken();

        $options = [
            'headers' => $headers,
            'query' => $request->query(),
            'body' => $request->getContent(),
        ];

        $method = strtoupper($request->getMethod());
        $uri = ltrim($request->getPathInfo(), '/');

        return $client->request($method, $uri, $options);
    }
}
