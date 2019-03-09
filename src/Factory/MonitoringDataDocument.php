<?php

declare(strict_types=1);

namespace App\Factory;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use DateTimeInterface;

interface MonitoringDataDocument
{
    public function createFrom(
        MonitoringDataDto $monitoringDataDto,
        DateTimeInterface $statusChangedAt
    ): MonitoringData;
}
