<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Repository\MonitoringDataRepository;

class PersistMonitoringDataService implements PersistMonitoringData
{
    private $monitoringDataRepository;

    public function __construct(MonitoringDataRepository $monitoringDataRepository)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
    }

    public function invoke(MonitoringDataDto $monitoringDataDto): void
    {
        $monitoringDataDocument = new MonitoringData(
            $monitoringDataDto->getId(),
            $monitoringDataDto->getStatus(),
            $monitoringDataDto->getPayload(),
            $monitoringDataDto->getPriority(),
            $monitoringDataDto->getIdleTimeoutInSeconds(),
            $monitoringDataDto->getDate()
        );

        $this->monitoringDataRepository->save($monitoringDataDocument);
    }
}
