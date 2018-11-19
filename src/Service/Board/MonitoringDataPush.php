<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Dto\MonitoringData;
use App\Exception\PushClientException;

interface MonitoringDataPush
{
    /**
     * @throws PushClientException
     */
    public function invoke(MonitoringData $monitoringData): void;
}
