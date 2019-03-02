<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;
use App\Exception\PersistenceLayerException;
use App\Repository\MonitoringDataRepository;
use Psr\Log\LoggerInterface;

class PersistGrowingTilesService implements PersistGrowingTiles
{
    private $monitoringDataRepository;
    private $logger;

    public function __construct(MonitoringDataRepository $monitoringDataRepository, LoggerInterface $logger)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
        $this->logger = $logger;
    }

    public function invoke(array $monitorings): array
    {
        $persistedMonitorings = [];

        /** @var MonitoringData $monitoring */
        foreach ($monitorings as $monitoring) {
            try {
                $this->monitoringDataRepository->save($monitoring);
                $persistedMonitorings[] = $monitoring;
            } catch (PersistenceLayerException $exception) {
                $this->logger->error(
                    sprintf('GROWING TILES: %s', $exception->getMessage()),
                    ['exception' => $exception]
                );
            }
        }

        return $persistedMonitorings;
    }
}
