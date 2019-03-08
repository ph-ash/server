<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Dto\MonitoringData;
use App\Exception\PersistenceLayerException;
use DateTimeInterface;

interface StatusChanged
{
    /**
     * @throws PersistenceLayerException
     */
    public function calculate(MonitoringData $monitoringData): DateTimeInterface;
}
