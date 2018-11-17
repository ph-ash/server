<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\IncomingMonitoringDataEvent;
use App\Service\Board\MonitoringDataPush;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BoardSubscriber implements EventSubscriberInterface

{
    private $monitoringDataPush;

    public function __construct(MonitoringDataPush $monitoringDataPush)
    {
        $this->monitoringDataPush = $monitoringDataPush;
    }

    public static function getSubscribedEvents()
    {
        return [IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => 'pushDataToBoard'];
    }

    public function pushDataToBoard(IncomingMonitoringDataEvent $incomingMonitoringDataEvent)
    {
        //TODO test
        //TODO exception handling
        $this->monitoringDataPush->invoke($incomingMonitoringDataEvent->getMonitoringData());
    }
}
