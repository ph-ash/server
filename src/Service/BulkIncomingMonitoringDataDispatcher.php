<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\BulkMonitoringData;
use App\Exception\BulkValidationException;
use App\Exception\PersistenceLayerException;
use OutOfBoundsException;

interface BulkIncomingMonitoringDataDispatcher
{
    /**
     * @throws PersistenceLayerException
     * @throws OutOfBoundsException
     * @throws BulkValidationException
     */
    public function invoke(BulkMonitoringData $monitoringData): void;
}
