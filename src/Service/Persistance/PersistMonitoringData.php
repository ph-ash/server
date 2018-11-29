<?php

declare(strict_types=1);

namespace App\Service\Persistance;

use App\Dto\MonitoringData;
use App\Exception\PersistenceLayerException;

interface PersistMonitoringData
{
    /**
     * @throws PersistenceLayerException
     */
    public function invoke(MonitoringData $monitoringData);
}
