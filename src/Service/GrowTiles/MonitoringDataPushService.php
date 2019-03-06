<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Exception\PushClientException;
use App\Factory\MonitoringDataDtoFactory;
use App\Service\Board\MonitoringDataPush as MonitoringDataDtoPush;
use Psr\Log\LoggerInterface;

class MonitoringDataPushService implements MonitoringDataPush
{
    private $monitoringDataPush;
    private $logger;
    private $monitoringDataDtoFactory;

    public function __construct(
        MonitoringDataDtoPush $monitoringDataPush,
        MonitoringDataDtoFactory $monitoringDataDtoFactory,
        LoggerInterface $logger
    ) {
        $this->monitoringDataPush = $monitoringDataPush;
        $this->monitoringDataDtoFactory = $monitoringDataDtoFactory;
        $this->logger = $logger;
    }

    public function invoke(array $monitorings): void
    {
        foreach ($monitorings as $monitoring) {
            $dto = $this->monitoringDataDtoFactory->create($monitoring);
            try {
                $this->monitoringDataPush->invoke($dto);
            } catch (PushClientException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }
    }
}
