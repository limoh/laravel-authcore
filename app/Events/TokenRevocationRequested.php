<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


// app/Events/TokenRevocationRequested.php
class TokenRevocationRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tokenId;
    public $reason;

    public function __construct(string $tokenId, string $reason = null)
    {
        $this->tokenId = $tokenId;
        $this->reason = $reason;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('revocations');
    }
}

