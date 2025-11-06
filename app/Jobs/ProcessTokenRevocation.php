<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

// app/Jobs/ProcessTokenRevocation.php
class ProcessTokenRevocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tokenId;
    public $reason;

    public function __construct($tokenId, $reason = null)
    {
        $this->tokenId = $tokenId;
        $this->reason = $reason;
    }

    public function handle()
    {
        // revoke in DB
        \DB::table('oauth_access_tokens')->where('id', $this->tokenId)->update(['revoked' => true]);
        \DB::table('oauth_refresh_tokens')->where('access_token_id', $this->tokenId)->update(['revoked' => true]);

        // push to redis blacklist for immediate checks
        \Illuminate\Support\Facades\Redis::setex("blacklist:token:{$this->tokenId}", 3600, 'revoked');

        // Audit
        \App\Services\AuditService::log(null, 'token.revoked.job', ['token' => $this->tokenId, 'reason' => $this->reason]);

        // Notify registered services (send POST to their /internal/revocations endpoint)
        $services = \DB::table('app_clients')->whereNotNull('revocation_endpoint')->pluck('revocation_endpoint');

        $client = new \GuzzleHttp\Client(['timeout' => 5]);
        foreach ($services as $endpoint) {
            try {
                $client->post($endpoint, [
                    'json' => ['token_id' => $this->tokenId, 'reason' => $this->reason],
                    // include internal auth header (see Gateway section)
                    'headers' => ['Authorization' => 'Bearer '. $this->internalServiceToken()]
                ]);
            } catch (\Exception $e) {
                // log but don't fail the job
                \Log::warning("Failed notify {$endpoint}: " . $e->getMessage());
            }
        }
    }

    protected function internalServiceToken()
    {
        // Issue or retrieve a signed short-lived internal token (JWT) for service-to-service calls.
        // For simplicity: create HMAC-signed token or issue from a trusted oauth client.
        return cache()->remember('internal_service_token', 55, function () {
            return \Str::random(60); // replace with real token issuance
        });
    }
}
