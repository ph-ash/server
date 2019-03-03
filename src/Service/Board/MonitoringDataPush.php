<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Dto\MonitoringData;
use App\Exception\ZMQClientException;

interface MonitoringDataPush
{
    /**
     * @throws ZMQClientException
     */
    public function invoke(MonitoringData $monitoringData): void;
}
