<?php

namespace App\MessageHandler;

use App\Message\HeartbeatMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class HeartbeatMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(HeartbeatMessage $message): void
    {
        $this->logger->info($message->getMessage());
    }
}
