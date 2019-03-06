<?php

declare(strict_types=1);

namespace App\Service;

use App\Event\DeleteMonitoringDataEvent as Event;
use App\Factory\DeleteMonitoringDataEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteMonitoringDataDispatcherService implements DeleteMonitoringDataDispatcher
{
    private $eventDispatcher;
    private $deleteMonitoringDataEvent;

    public function __construct(EventDispatcherInterface $eventDispatcher, DeleteMonitoringDataEvent $deleteMonitoringDataEvent)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->deleteMonitoringDataEvent = $deleteMonitoringDataEvent;
    }

    public function invoke(string $monitoringDataId): void
    {
        $deleteMonitoringDataEvent = $this->deleteMonitoringDataEvent->createFrom($monitoringDataId);
        $this->eventDispatcher->dispatch(Event::EVENT_DELETE_MONITORING_DATA, $deleteMonitoringDataEvent);
    }
}
