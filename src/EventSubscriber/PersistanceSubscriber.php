<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\DeleteMonitoringDataEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Exception\PersistenceLayerException;
use App\Factory\MonitoringDataDocument;
use App\Repository\MonitoringData as MonitoringDataRepository;
use App\Service\Persistance\Priority;
use App\Service\Persistance\StatusChanged;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistanceSubscriber implements EventSubscriberInterface
{
    private $monitoringDataRepository;
    private $monitoringDataDocument;
    private $statusChanged;
    private $priority;

    public function __construct(
        MonitoringDataRepository $monitoringDataRepository,
        MonitoringDataDocument $monitoringDataDocument,
        StatusChanged $statusChanged,
        Priority $priority
    ) {
        $this->monitoringDataRepository = $monitoringDataRepository;
        $this->monitoringDataDocument = $monitoringDataDocument;
        $this->statusChanged = $statusChanged;
        $this->priority = $priority;
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
        $monitoringDataId = $deleteMonitoringDataEvent->getMonitoringDataId();
        $this->monitoringDataRepository->delete($monitoringDataId);
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

        $monitoringDataDocument = $this->monitoringDataDocument->createFrom($monitoringDataDto, $statusChangedAt);
        $event->setMonitoringData($monitoringDataDto);
        $this->monitoringDataRepository->save($monitoringDataDocument);
    }
}
