<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;

class DetermineGrowingTilesService implements DetermineGrowingTiles
{
    private $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function invoke(array $monitorings): array
    {
        $updatedMonitorings = [];

        /** @var MonitoringData $monitoring */
        foreach ($monitorings as $monitoring) {
            $newPriority = $this->calculator->calculateNewPriority($monitoring);
            if ($newPriority !== $monitoring->getPriority()) {
                $monitoring->setPriority($newPriority);
                $updatedMonitorings[] = $monitoring;
            }
        }

        return $updatedMonitorings;
    }
}
