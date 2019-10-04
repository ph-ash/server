<?php

declare(strict_types=1);

namespace App\Factory;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\Outgoing\MonitoringData as OutgoingMonitoringDataDto;

interface MonitoringDataDtoFactory
{
    public function createOutgoingFromDocument(MonitoringDataDocument $monitoringData): OutgoingMonitoringDataDto;
}
