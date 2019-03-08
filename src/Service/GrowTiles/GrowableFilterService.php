<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;
use DateTimeImmutable;

class GrowableFilterService implements GrowableFilter
{
    /** naive try to take update routines into account: let monitorings be growable a few seconds earlier */
    private const LAMBDA = 3;

    public function isGrowable(MonitoringData $monitoringData): bool
    {
        $intervalCount = $monitoringData->getTileExpansionIntervalCount() ?? 1;
        $lastGrown = $monitoringData->getLastTileExpansion() ?? $monitoringData->getStatusChangedAt();

        $lastGrownFromNow = new DateTimeImmutable(
            sprintf('- %d second', $intervalCount * $monitoringData->getIdleTimeoutInSeconds() - self::LAMBDA)
        );

        return $lastGrown < $lastGrownFromNow;
    }
}
