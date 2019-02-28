<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\DeleteMonitoringDataEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Exception\PushClientException;
use App\Service\Board\MonitoringDataPush;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BoardSubscriber implements EventSubscriberInterface
{
    private $monitoringDataPush;

    public function __construct(MonitoringDataPush $monitoringDataPush)
    {
        $this->monitoringDataPush = $monitoringDataPush;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => ['pushDataToBoard', -20],
            DeleteMonitoringDataEvent::EVENT_DELETE_MONITORING_DATA => ['onDeleteMonitoringData', -20]
        ];
    }

    public function onDeleteMonitoringData(DeleteMonitoringDataEvent $deleteMonitoringDataEvent): void
    {
        $monitoringDataDto = $deleteMonitoringDataEvent->getMonitoringData();
        //TODO implement deletion in board
    }

    /**
     * @throws PushClientException
     */
    public function pushDataToBoard(IncomingMonitoringDataEvent $incomingMonitoringDataEvent): void
    {
        //TODO exception handling
        $this->monitoringDataPush->invoke($incomingMonitoringDataEvent->getMonitoringData());
    }
}
