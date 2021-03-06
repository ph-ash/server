<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Exception\ZMQClientException;
use App\Factory\MonitoringDataDtoFactory;
use App\Service\Board\MonitoringDataPush as MonitoringDataDtoPush;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

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
            $dto = $this->monitoringDataDtoFactory->createOutgoingFromDocument($monitoring);
            try {
                $this->monitoringDataPush->invoke($dto);
            } catch (ZMQClientException | UnexpectedValueException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }
    }
}
