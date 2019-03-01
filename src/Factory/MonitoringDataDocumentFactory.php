<?php

declare(strict_types=1);

namespace App\Factory;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;

class MonitoringDataDocumentFactory implements MonitoringDataDocument
{
    public function createFrom(MonitoringDataDto $monitoringDataDto): MonitoringData
    {
        return new MonitoringData(
            $monitoringDataDto->getId(),
            $monitoringDataDto->getStatus(),
            $monitoringDataDto->getPayload(),
            $monitoringDataDto->getPriority(),
            $monitoringDataDto->getIdleTimeoutInSeconds(),
            $monitoringDataDto->getDate(),
            $monitoringDataDto->getPath()
        );
    }
}
