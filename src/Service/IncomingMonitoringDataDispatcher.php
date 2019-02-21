<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MonitoringData;
use App\Exception\PersistenceLayerException;
use OutOfBoundsException;
use Symfony\Component\Validator\Exception\ValidatorException;

interface IncomingMonitoringDataDispatcher
{
    /**
     * @throws PersistenceLayerException
     * @throws ValidatorException
     * @throws OutOfBoundsException
     */
    public function invoke(MonitoringData $monitoringData): void;
}
