<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MonitoringData;
use App\Exception\PersistenceLayerException;

interface IncomingMonitoringDataDispatcher
{
    /**
     * @throws PersistenceLayerException
     */
    public function invoke(MonitoringData $monitoringData): void;
}
