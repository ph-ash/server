<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\GrowTilesEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Exception\PushClientException;
use App\Service\Board\MonitoringDataPush as MonitoringDataDtoPush;
use App\Service\GrowTiles\MonitoringDataPush as MonitoringDataDocumentPush;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BoardSubscriber implements EventSubscriberInterface
{
    private $monitoringDataDtoPush;
    private $monitoringDataDocumentPush;

    public function __construct(
        MonitoringDataDtoPush $monitoringDataDtoPush,
        MonitoringDataDocumentPush $monitoringDataDocumentPush
    ) {
        $this->monitoringDataDtoPush = $monitoringDataDtoPush;
        $this->monitoringDataDocumentPush = $monitoringDataDocumentPush;
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
        $this->monitoringDataDtoPush->invoke($incomingMonitoringDataEvent->getMonitoringData());
    }

    public function pushUpdatedDataToBoard(GrowTilesEvent $event): void
    {
        $this->monitoringDataDocumentPush->invoke($event->getMonitorings());
    }
}
