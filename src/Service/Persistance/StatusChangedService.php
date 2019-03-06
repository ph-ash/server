<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Dto\MonitoringData;
use App\Repository\MonitoringDataRepository;
use DateTimeInterface;

class StatusChangedService implements StatusChanged
{
    private $monitoringDataRepository;

    public function __construct(MonitoringDataRepository $monitoringDataRepository)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
    }

    public function calculate(MonitoringData $monitoringData): DateTimeInterface
    {
        $oldMonitoringDataDocument = $this->monitoringDataRepository->find($monitoringData->getId());
        $statusChangedAt = $monitoringData->getDate();

        if ($oldMonitoringDataDocument && $oldMonitoringDataDocument->getStatus() === $monitoringData->getStatus()) {
            $statusChangedAt = $oldMonitoringDataDocument->getStatusChangedAt();
        }

        return $statusChangedAt;
    }
}
