<?php

namespace App\Message;

class HeartbeatMessage
{
    public function __construct(
        private readonly string $message = 'Yollab scheduler heartbeat'
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
