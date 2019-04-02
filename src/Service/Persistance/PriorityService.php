<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Dto\MonitoringData;
use App\Enum\MonitoringStatus;
use App\Repository\MonitoringDataRepository;

class PriorityService implements Priority
{
    private $monitoringDataRepository;

    public function __construct(MonitoringDataRepository $monitoringDataRepository)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
    }

    public function calculate(MonitoringData $monitoringData): int
    {
        $oldMonitoringDataDocument = $this->monitoringDataRepository->find($monitoringData->getId());
        $priority = $monitoringData->getPriority();

        if ($oldMonitoringDataDocument &&
            $oldMonitoringDataDocument->getPriority() > $monitoringData->getPriority() &&
            $monitoringData->getStatus() === MonitoringStatus::ERROR()->getValue()
        ) {
            $priority = $oldMonitoringDataDocument->getPriority();
        }

        return $priority;
    }
}
