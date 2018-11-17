<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use App\Dto\MonitoringData;

class PushClientService implements PushClient
{
    public function send(MonitoringData $monitoringData)
    {
        //TODO implement ZMQ send
        //TODO test
        //TODO exception handling
    }
}
