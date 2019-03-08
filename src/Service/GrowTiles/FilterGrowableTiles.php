<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;

interface FilterGrowableTiles
{
    /**
     * @param MonitoringData[] $monitorings
     * @return MonitoringData[]
     */
    public function invoke(array $monitorings): array;
}
