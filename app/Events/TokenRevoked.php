<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokenRevoked
{
    use Dispatchable, SerializesModels;

    public string $tokenId;

    public function __construct(string $tokenId)
    {
        $this->tokenId = $tokenId;
    }
}
