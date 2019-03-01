<?php

declare(strict_types=1);

namespace App\Factory;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;

interface MonitoringDataDocument
{
    public function createFrom(MonitoringDataDto $monitoringDataDto): MonitoringData;
}
