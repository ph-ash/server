<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MonitoringData;
use App\Exception\PersistenceLayerException;
use App\Exception\ValidationException;
use OutOfBoundsException;

interface IncomingMonitoringDataDispatcher
{
    /**
     * @throws PersistenceLayerException
     * @throws ValidationException
     * @throws OutOfBoundsException
     */
    public function invoke(MonitoringData $monitoringData): void;
}
