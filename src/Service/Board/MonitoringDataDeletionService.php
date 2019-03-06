<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Service\Board\ZMQ\Client;
use App\ValueObject\Channel;
use Symfony\Component\Serializer\SerializerInterface;

class MonitoringDataDeletionService implements MonitoringDataDeletion
{
    private $pushClient;
    private $serializer;

    public function __construct(Client $pushClient, SerializerInterface $serializer)
    {
        $this->pushClient = $pushClient;
        $this->serializer = $serializer;
    }

    public function invoke(string $monitoringDataId): void
    {
        $channel = new Channel(Channel::DELETE);
        $this->pushClient->send($this->serializer->serialize($monitoringDataId, 'json'), $channel);
    }
}
