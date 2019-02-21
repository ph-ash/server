<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\BulkMonitoringData;
use App\Exception\PersistenceLayerException;
use OutOfBoundsException;
use Symfony\Component\Validator\Exception\ValidatorException;

interface BulkIncomingMonitoringDataDispatcher
{
    /**
     * @throws PersistenceLayerException
     * @throws ValidatorException
     * @throws OutOfBoundsException
     */
    public function invoke(BulkMonitoringData $monitoringData): void;
}
