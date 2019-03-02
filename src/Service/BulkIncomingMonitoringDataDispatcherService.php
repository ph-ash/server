<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\BulkMonitoringData;
use App\Exception\BulkValidationException;
use App\Exception\ValidationException;

class BulkIncomingMonitoringDataDispatcherService implements BulkIncomingMonitoringDataDispatcher
{
    private $incomingMonitoringDataDispatcher;

    public function __construct(IncomingMonitoringDataDispatcher $incomingMonitoringDataDispatcher)
    {
        $this->incomingMonitoringDataDispatcher = $incomingMonitoringDataDispatcher;
    }

    public function invoke(BulkMonitoringData $bulkMonitoringData): void
    {
        $validationExceptions = [];
        foreach ($bulkMonitoringData->getMonitoringData() as $monitoringData) {
            try {
                $this->incomingMonitoringDataDispatcher->invoke($monitoringData);
            } catch (ValidationException $exception) {
                $validationExceptions[] = $exception;
            }
        }

        if (!empty($validationExceptions)) {
            throw new BulkValidationException($validationExceptions);
        }
    }
}
