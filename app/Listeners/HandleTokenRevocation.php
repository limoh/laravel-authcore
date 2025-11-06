<?php

namespace App\Listeners;

use App\Events\TokenRevoked;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HandleTokenRevocation
{
    public function handle(TokenRevoked $event): void
    {
        // Set revoked=true in DB
        DB::table('oauth_access_tokens')
            ->where('id', $event->tokenId)
            ->update(['revoked' => true]);

        Log::info("Token revoked: {$event->tokenId}");
    }
}
