<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;

class CalculatorService implements Calculator
{
    public function calculateNewPriority(MonitoringData $monitoringData): int
    {
        // TODO
        return $monitoringData->getPriority();
    }
}
