<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Dto\MonitoringData;
use App\Exception\PersistenceLayerException;

interface Priority
{
    /**
     * @throws PersistenceLayerException
     */
    public function calculate(MonitoringData $monitoringData): int;
}
