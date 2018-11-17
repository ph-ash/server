<?php

namespace App\Service\Board\ZMQ;

use App\Dto\MonitoringData;

interface PushClient
{
    public function send(MonitoringData $monitoringData): void;
}
