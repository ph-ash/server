<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Repository\MonitoringDataRepository;
use DateTimeInterface;

class PersistMonitoringDataService implements PersistMonitoringData
{
    private $monitoringDataRepository;

    public function __construct(MonitoringDataRepository $monitoringDataRepository)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
    }

    public function invoke(MonitoringDataDto $monitoringDataDto, DateTimeInterface $statusChangedAt): void
    {
        $monitoringDataDocument = new MonitoringData(
            $monitoringDataDto->getId(),
            $monitoringDataDto->getStatus(),
            $statusChangedAt,
            $monitoringDataDto->getPayload(),
            $monitoringDataDto->getPriority(),
            $monitoringDataDto->getIdleTimeoutInSeconds(),
            $monitoringDataDto->getDate(),
            $monitoringDataDto->getPath(),
            $monitoringDataDto->getTileExpansionIntervalCount(),
            $monitoringDataDto->getTileExpansionGrowthExpression()
        );

        $this->monitoringDataRepository->save($monitoringDataDocument);
    }
}
