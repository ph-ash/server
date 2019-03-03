<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\DeleteMonitoringDataEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Exception\ZMQClientException;
use App\Service\Board\MonitoringDataPush;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BoardSubscriber implements EventSubscriberInterface
{
    private $monitoringDataPush;
    private $monitoringDataDeletion;

    public function __construct(MonitoringDataPush $monitoringDataPush, MonitoringDataPush $monitoringDataDeletion)
    {
        $this->monitoringDataPush = $monitoringDataPush;
        $this->monitoringDataDeletion = $monitoringDataDeletion;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => ['pushDataToBoard', -20],
            DeleteMonitoringDataEvent::EVENT_DELETE_MONITORING_DATA => ['onDeleteMonitoringData', -20]
        ];
    }

    /**
     * @throws ZMQClientException
     */
    public function onDeleteMonitoringData(DeleteMonitoringDataEvent $deleteMonitoringDataEvent): void
    {
        $this->monitoringDataDeletion->invoke($deleteMonitoringDataEvent->getMonitoringData());
    }

    /**
     * @throws ZMQClientException
     */
    public function pushDataToBoard(IncomingMonitoringDataEvent $incomingMonitoringDataEvent): void
    {
        $this->monitoringDataPush->invoke($incomingMonitoringDataEvent->getMonitoringData());
    }
}
