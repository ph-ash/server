<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;

interface MonitoringDataPush
{
    /**
     * @param MonitoringData[] $monitorings
     */
    public function invoke(array $monitorings): void;
}
