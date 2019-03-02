<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Exception\PersistenceLayerException;
use App\Repository\MonitoringDataRepository;
use DateTimeInterface;

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
            $this->getStatusChangedAt($monitoringDataDto),
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

    /**
     * @throws PersistenceLayerException
     */
    private function getStatusChangedAt(MonitoringDataDto $monitoringDataDto): DateTimeInterface
    {
        $statusChangedAt = $monitoringDataDto->getDate();

        $oldMonitoringDataDocument = $this->monitoringDataRepository->find($monitoringDataDto->getId());
        if ($oldMonitoringDataDocument && $oldMonitoringDataDocument->getStatus() === $monitoringDataDto->getStatus()) {
            $statusChangedAt = $oldMonitoringDataDocument->getStatusChangedAt();
        }

        return $statusChangedAt;
    }
}
