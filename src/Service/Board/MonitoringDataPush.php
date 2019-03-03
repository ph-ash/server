<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Dto\MonitoringData;
use App\Exception\ZMQClientException;
use UnexpectedValueException;

interface MonitoringDataPush
{
    /**
     * @throws ZMQClientException
     * @throws UnexpectedValueException
     */
    public function invoke(MonitoringData $monitoringData): void;
}
