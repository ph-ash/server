<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\IncomingMonitoringDataEvent;
use App\Exception\PersistenceLayerException;
use App\Service\Persistance\PersistMonitoringData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistanceSubscriber implements EventSubscriberInterface
{
    private $persistMonitoringData;

    public function __construct(PersistMonitoringData $persistMonitoringData)
    {
        $this->persistMonitoringData = $persistMonitoringData;
    }

    public static function getSubscribedEvents(): array
    {
        //TODO add some priority
        return [IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => 'persistMonitoringData'];
    }

    /**
     * @throws PersistenceLayerException
     */
    public function persistMonitoringData(IncomingMonitoringDataEvent $event): void
    {
        $monitoringDataDto = $event->getMonitoringData();
        $this->persistMonitoringData->invoke($monitoringDataDto);
    }
}
