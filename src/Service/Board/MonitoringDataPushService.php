<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Dto\Outgoing\MonitoringData;
use App\Service\Board\ZMQ\Client;
use App\ValueObject\Channel;
use Symfony\Component\Serializer\SerializerInterface;

class MonitoringDataPushService implements MonitoringDataPush
{
    private $pushClient;
    private $serializer;

    public function __construct(Client $pushClient, SerializerInterface $serializer)
    {
        $this->pushClient = $pushClient;
        $this->serializer = $serializer;
    }

    public function invoke(MonitoringData $monitoringData): void
    {
        $channel = new Channel(Channel::PUSH);
        $this->pushClient->send($this->serializer->serialize($monitoringData, 'json'), $channel);
    }
}
