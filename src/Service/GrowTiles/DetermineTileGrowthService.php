<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;

class DetermineTileGrowthService implements DetermineTileGrowth
{
    private $priorityGrowth;
    private $logger;

    public function __construct(PriorityGrowth $priorityGrowth, LoggerInterface $logger)
    {
        $this->priorityGrowth = $priorityGrowth;
        $this->logger = $logger;
    }

    public function invoke(array $monitorings): array
    {
        $updatedMonitorings = [];

        /** @var MonitoringData $monitoring */
        foreach ($monitorings as $monitoring) {
            $newPriority = $this->priorityGrowth->calculateNewPriority($monitoring);
            if ($newPriority !== $monitoring->getPriority()) {
                try {
                    $monitoring->setPriority($newPriority);
                    $monitoring->setLastTileExpansion(new DateTimeImmutable());
                    $updatedMonitorings[] = $monitoring;
                } catch (Exception $exception) {
                    $this->logger->error(
                        sprintf('GROWING TILES: %s', $exception->getMessage()),
                        ['exception' => $exception]
                    );
                }
            }
        }

        return $updatedMonitorings;
    }
}
