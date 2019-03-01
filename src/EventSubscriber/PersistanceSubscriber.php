<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\DeleteMonitoringDataEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Exception\PersistenceLayerException;
use App\Factory\MonitoringDataDocument;
use App\Repository\MonitoringData as MonitoringDataRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistanceSubscriber implements EventSubscriberInterface
{

    private $monitoringDataRepository;
    private $monitoringDataDocument;

    public function __construct(MonitoringDataRepository $monitoringDataRepository, MonitoringDataDocument $monitoringDataDocument)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
        $this->monitoringDataDocument = $monitoringDataDocument;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => ['persistMonitoringData', -10],
            DeleteMonitoringDataEvent::EVENT_DELETE_MONITORING_DATA => ['onDeleteMonitoringData', -10]
        ];
    }

    /**
     * @throws PersistenceLayerException
     */
    public function onDeleteMonitoringData(DeleteMonitoringDataEvent $deleteMonitoringDataEvent): void
    {
        $monitoringDataDto = $deleteMonitoringDataEvent->getMonitoringData();
        $this->monitoringDataRepository->delete($monitoringDataDto->getId());
    }

    /**
     * @throws PersistenceLayerException
     */
    public function persistMonitoringData(IncomingMonitoringDataEvent $event): void
    {
        $monitoringDataDto = $event->getMonitoringData();
        $monitoringDataDocument = $this->monitoringDataDocument->createFrom($monitoringDataDto);
        $this->monitoringDataRepository->save($monitoringDataDocument);
    }
}
