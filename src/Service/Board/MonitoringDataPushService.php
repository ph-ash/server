<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Dto\MonitoringData;
use App\Service\Board\ZMQ\PushClient;

class MonitoringDataPushService implements MonitoringDataPush
{
    private $pushClient;

    public function __construct(PushClient $pushClient)
    {
        $this->pushClient = $pushClient;
    }

    public function invoke(MonitoringData $monitoringData): void
    {
        //TODO test
        //TODO exception handling
        $this->pushClient->send($monitoringData);
    }
}

