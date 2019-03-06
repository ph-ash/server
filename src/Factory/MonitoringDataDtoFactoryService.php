<?php

declare(strict_types=1);

namespace App\Factory;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\MonitoringData as MonitoringDataDto;

class MonitoringDataDtoFactoryService implements MonitoringDataDtoFactory
{
    public function create(MonitoringDataDocument $monitoringData): MonitoringDataDto
    {
        return new MonitoringDataDto(
            $monitoringData->getId(),
            $monitoringData->getStatus(),
            $monitoringData->getPayload(),
            $monitoringData->getPriority(),
            $monitoringData->getIdleTimeoutInSeconds(),
            $monitoringData->getDate(),
            $monitoringData->getPath(),
            $monitoringData->getTileExpansionIntervalCount(),
            $monitoringData->getTileExpansionGrowthExpression()
        );
    }
}
