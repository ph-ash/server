<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;

interface PriorityGrowth
{
    public function calculateNewPriority(MonitoringData $monitoringData): int;
}
