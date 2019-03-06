<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Dto\MonitoringData;
use App\Exception\PersistenceLayerException;
use DateTimeInterface;

interface PersistMonitoringData
{
    /**
     * @throws PersistenceLayerException
     */
    public function invoke(MonitoringData $monitoringData, DateTimeInterface $statusChangedAt): void;
}
