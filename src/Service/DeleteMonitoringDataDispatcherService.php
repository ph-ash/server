<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MonitoringData;
use App\Event\DeleteMonitoringDataEvent as Event;
use App\Exception\PersistenceLayerException;
use App\Exception\ValidationException;
use App\Factory\DeleteMonitoringDataEvent;
use OutOfBoundsException;
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

    public function invoke(MonitoringData $monitoringData): void
    {
        $deleteMonitoringDataEvent = $this->deleteMonitoringDataEvent->createFrom($monitoringData);
        $this->eventDispatcher->dispatch(Event::EVENT_DELETE_MONITORING_DATA, $deleteMonitoringDataEvent);
    }
}
