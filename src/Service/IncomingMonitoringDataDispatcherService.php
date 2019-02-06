<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MonitoringData;
use App\Event\IncomingMonitoringDataEvent as Event;
use App\Factory\IncomingMonitoringDataEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class IncomingMonitoringDataDispatcherService implements IncomingMonitoringDataDispatcher
{
    private $eventDispatcher;
    private $incomingMonitoringDataEvent;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        IncomingMonitoringDataEvent $incomingMonitoringDataEvent
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->incomingMonitoringDataEvent = $incomingMonitoringDataEvent;
    }

    public function invoke(MonitoringData $monitoringData): void
    {
        $event = $this->incomingMonitoringDataEvent->createFrom($monitoringData);
        $this->eventDispatcher->dispatch(Event::EVENT_INCOMING_MONITORING_DATA, $event);
    }
}
