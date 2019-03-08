<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;
use Exception;
use Psr\Log\LoggerInterface;

class FilterGrowableTilesService implements FilterGrowableTiles
{
    private $growableFilter;
    private $logger;

    public function __construct(GrowableFilter $growableFilter, LoggerInterface $logger)
    {
        $this->growableFilter = $growableFilter;
        $this->logger = $logger;
    }

    public function invoke(array $monitorings): array
    {
        $filteredMonitorings = [];

        /** @var MonitoringData $monitoring */
        foreach ($monitorings as $monitoring) {
            try {
                if ($this->growableFilter->isGrowable($monitoring)) {
                    $filteredMonitorings[] = $monitoring;
                }
            } catch (Exception $exception) {
                $this->logger->error(
                    sprintf('GROWING TILES: %s', $exception->getMessage()),
                    ['exception' => $exception]
                );
            }
        }

        return $filteredMonitorings;
    }
}
