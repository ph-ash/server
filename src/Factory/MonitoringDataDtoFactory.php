<?php

declare(strict_types=1);

namespace App\Factory;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\MonitoringData as MonitoringDataDto;

interface MonitoringDataDtoFactory
{
    public function create(MonitoringDataDocument $monitoringData): MonitoringDataDto;
}
