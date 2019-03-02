<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\GrowTilesEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Exception\PushClientException;
use App\Factory\MonitoringDataDtoFactory;
use App\Service\Board\MonitoringDataPush;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BoardSubscriber implements EventSubscriberInterface
{
    private $monitoringDataPush;
    private $logger;
    private $monitoringDataDtoFactory;

    public function __construct(
        MonitoringDataPush $monitoringDataPush,
        MonitoringDataDtoFactory $monitoringDataDtoFactory,
        LoggerInterface $logger
    ) {
        $this->monitoringDataPush = $monitoringDataPush;
        $this->monitoringDataDtoFactory = $monitoringDataDtoFactory;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => ['pushDataToBoard', -20],
            GrowTilesEvent::EVENT_NAME => ['pushUpdatedDataToBoard', -20]
        ];
    }

    /**
     * @throws PushClientException
     */
    public function pushDataToBoard(IncomingMonitoringDataEvent $incomingMonitoringDataEvent): void
    {
        //TODO exception handling
        $this->monitoringDataPush->invoke($incomingMonitoringDataEvent->getMonitoringData());
    }

    public function pushUpdatedDataToBoard(GrowTilesEvent $event): void
    {
        foreach ($event->getMonitorings() as $monitoring) {
            $dto = $this->monitoringDataDtoFactory->createFrom($monitoring);
            try {
                $this->monitoringDataPush->invoke($dto);
            } catch (PushClientException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }
    }
}
