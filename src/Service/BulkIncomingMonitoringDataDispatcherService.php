<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\BulkMonitoringData;
use App\Exception\BulkValidationException;
use Symfony\Component\Validator\Exception\ValidatorException;

class BulkIncomingMonitoringDataDispatcherService implements BulkIncomingMonitoringDataDispatcher
{
    private $incomingMonitoringDataDispatcher;

    public function __construct(IncomingMonitoringDataDispatcher $incomingMonitoringDataDispatcher)
    {
        $this->incomingMonitoringDataDispatcher = $incomingMonitoringDataDispatcher;
    }

    public function invoke(BulkMonitoringData $bulkMonitoringData): void
    {
        $validatorExceptions = [];
        foreach ($bulkMonitoringData->getMonitoringData() as $monitoringData) {
            try {
                $this->incomingMonitoringDataDispatcher->invoke($monitoringData);
            } catch (ValidatorException $exception) {
                $validatorExceptions[] = $exception;
            }
        }

        if (!empty($validatorExceptions)) {
            throw new BulkValidationException($validatorExceptions);
        }
    }
}
