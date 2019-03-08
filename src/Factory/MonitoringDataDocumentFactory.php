<?php

declare(strict_types=1);

namespace App\Factory;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use DateTimeInterface;

class MonitoringDataDocumentFactory implements MonitoringDataDocument
{
    public function createFrom(MonitoringDataDto $monitoringDataDto, DateTimeInterface $statusChangedAt): MonitoringData
    {
        return new MonitoringData(
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
    }
}
