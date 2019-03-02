<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Factory\MonitoringDataDtoFactory;
use App\Repository\MonitoringDataRepository;
use DateTimeInterface;

class PersistMonitoringDataService implements PersistMonitoringData
{
    private $monitoringDataRepository;
    private $monitoringDataDtoFactory;

    public function __construct(MonitoringDataRepository $monitoringDataRepository, MonitoringDataDtoFactory $monitoringDataDtoFactory)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
        $this->monitoringDataDtoFactory = $monitoringDataDtoFactory;
    }

    public function invoke(MonitoringDataDto $monitoringDataDto): MonitoringDataDto
    {
        $oldMonitoringDataDocument = $this->monitoringDataRepository->find($monitoringDataDto->getId());

        $monitoringDataDocument = new MonitoringData(
            $monitoringDataDto->getId(),
            $monitoringDataDto->getStatus(),
            $this->getStatusChangedAt($monitoringDataDto, $oldMonitoringDataDocument),
            $monitoringDataDto->getPayload(),
            $this->getPriority($monitoringDataDto, $oldMonitoringDataDocument),
            $monitoringDataDto->getIdleTimeoutInSeconds(),
            $monitoringDataDto->getDate(),
            $monitoringDataDto->getPath(),
            $monitoringDataDto->getTileExpansionIntervalCount(),
            $monitoringDataDto->getTileExpansionGrowthExpression()
        );

        $this->monitoringDataRepository->save($monitoringDataDocument);

        return $this->monitoringDataDtoFactory->createFrom($monitoringDataDocument);
    }

    private function getStatusChangedAt(MonitoringDataDto $monitoringDataDto, ?MonitoringData $oldMonitoringDataDocument): DateTimeInterface
    {
        $statusChangedAt = $monitoringDataDto->getDate();

        if ($oldMonitoringDataDocument && $oldMonitoringDataDocument->getStatus() === $monitoringDataDto->getStatus()) {
            $statusChangedAt = $oldMonitoringDataDocument->getStatusChangedAt();
        }

        return $statusChangedAt;
    }

    private function getPriority(MonitoringDataDto $monitoringDataDto, ?MonitoringData $oldMonitoringDataDocument): int
    {
        $priority = $monitoringDataDto->getPriority();

        if ($oldMonitoringDataDocument && $oldMonitoringDataDocument->getPriority() > $monitoringDataDto->getPriority()) {
            $priority = $oldMonitoringDataDocument->getPriority();
        }

        return $priority;
    }
}
