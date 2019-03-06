<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\IncomingMonitoringDataEvent;
use App\Exception\PersistenceLayerException;
use App\Service\Persistance\PersistMonitoringData;
use App\Service\Persistance\Priority;
use App\Service\Persistance\StatusChanged;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistanceSubscriber implements EventSubscriberInterface
{
    private $persistMonitoringData;
    private $statusChanged;
    private $priority;

    public function __construct(
        PersistMonitoringData $persistMonitoringData,
        StatusChanged $statusChanged,
        Priority $priority
    ) {
        $this->persistMonitoringData = $persistMonitoringData;
        $this->statusChanged = $statusChanged;
        $this->priority = $priority;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => ['persistMonitoringData', -10]
        ];
    }

    /**
     * @throws PersistenceLayerException
     */
    public function persistMonitoringData(IncomingMonitoringDataEvent $event): void
    {
        $monitoringDataDto = $event->getMonitoringData();

        // calculate values, which depend on previously persisted data
        $statusChangedAt = $this->statusChanged->calculate($monitoringDataDto);
        $priority = $this->priority->calculate($monitoringDataDto);

        // update DTO to re-persist and communicate the correct data
        $monitoringDataDto->setPriority($priority);

        $this->persistMonitoringData->invoke($monitoringDataDto, $statusChangedAt, $priority);
        $event->setMonitoringData($monitoringDataDto);
    }
}
