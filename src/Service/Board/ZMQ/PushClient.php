<?php

declare(strict_types=1);

namespace App\Service\Board\ZMQ;

use App\Dto\MonitoringData;
use App\Exception\PushClientException;

interface PushClient
{
    /**
     * @throws PushClientException
     */
    public function send(MonitoringData $monitoringData): void;
}
