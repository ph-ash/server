<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\DeleteMonitoringDataEvent;
use App\Event\GrowTilesEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Exception\ZMQClientException;
use App\Service\Board\MonitoringDataDeletion;
use App\Service\Board\MonitoringDataPush as MonitoringDataDtoPush;
use App\Service\GrowTiles\MonitoringDataPush as MonitoringDataDocumentPush;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UnexpectedValueException;

class BoardSubscriber implements EventSubscriberInterface
{
    private $monitoringDataDtoPush;
    private $monitoringDataDocumentPush;
    private $monitoringDataDeletion;

    public function __construct(
        MonitoringDataDtoPush $monitoringDataDtoPush,
        MonitoringDataDocumentPush $monitoringDataDocumentPush
    ) {
        $this->monitoringDataDtoPush = $monitoringDataDtoPush;
        $this->monitoringDataDocumentPush = $monitoringDataDocumentPush;
        $this->monitoringDataDeletion = $monitoringDataDeletion;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => ['pushDataToBoard', -20],
            GrowTilesEvent::EVENT_NAME => ['pushUpdatedDataToBoard', -20]
            DeleteMonitoringDataEvent::EVENT_DELETE_MONITORING_DATA => ['onDeleteMonitoringData', -20]
        ];
    }

    /**
     * @throws ZMQClientException
     * @throws UnexpectedValueException
     */
    public function onDeleteMonitoringData(DeleteMonitoringDataEvent $deleteMonitoringDataEvent): void
    {
        $this->monitoringDataDeletion->invoke($deleteMonitoringDataEvent->getMonitoringDataId());
    }

    /**
     * @throws ZMQClientException
     * @throws UnexpectedValueException
     */
    public function pushDataToBoard(IncomingMonitoringDataEvent $incomingMonitoringDataEvent): void
    {
        $this->monitoringDataDtoPush->invoke($incomingMonitoringDataEvent->getMonitoringData());
    }

    public function pushUpdatedDataToBoard(GrowTilesEvent $event): void
    {
        $this->monitoringDataDocumentPush->invoke($event->getMonitorings());
    }
}
