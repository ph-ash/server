<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Dto\MonitoringData;
use App\Service\Board\ZMQ\Client;
use Symfony\Component\Serializer\SerializerInterface;

class MonitoringDataDeletionService implements MonitoringDataPush
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
        $this->pushClient->send($this->serializer->serialize($monitoringData->getId(), 'json'));
    }
}
