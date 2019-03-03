<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;
use Exception;

interface GrowableFilter
{
    /**
     * @throws Exception
     */
    public function isGrowable(MonitoringData $monitoringData): bool;
}
