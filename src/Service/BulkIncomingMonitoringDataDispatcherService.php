<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\BulkMonitoringData;
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
        foreach ($bulkMonitoringData->getMonitoringData() as $monitoringData) {
            try {
                $this->incomingMonitoringDataDispatcher->invoke($monitoringData);
            } catch (ValidatorException $exception) {
                //TODO catch and gather exceptions, return some response
            }
        }
    }
}
