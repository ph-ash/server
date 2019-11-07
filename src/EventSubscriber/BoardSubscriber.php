<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\DeleteMonitoringDataEvent;
use App\Event\GrowTilesEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Exception\ZMQClientException;
use App\Factory\MonitoringDataDtoFactory;
use App\Repository\MonitoringDataRepository;
use App\Service\Board\MonitoringDataDeletion;
use App\Service\Board\MonitoringDataPush as MonitoringDataDtoPush;
use App\Service\GrowTiles\MonitoringDataPush as MonitoringDataDocumentPush;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UnexpectedValueException;

class BoardSubscriber implements EventSubscriberInterface
{
    private $monitoringDataDtoPush;
    private $monitoringDataDocumentPush;
    private $monitoringDataDeletion;
    private $monitoringDataDtoFactory;
    private $monitoringDataRepository;

    public function __construct(
        MonitoringDataDtoPush $monitoringDataDtoPush,
        MonitoringDataDocumentPush $monitoringDataDocumentPush,
        MonitoringDataDeletion $monitoringDataDeletion,
        MonitoringDataDtoFactory $monitoringDataDtoFactory,
        MonitoringDataRepository $monitoringDataRepository
    ) {
        $this->monitoringDataDtoPush = $monitoringDataDtoPush;
        $this->monitoringDataDocumentPush = $monitoringDataDocumentPush;
        $this->monitoringDataDeletion = $monitoringDataDeletion;
        $this->monitoringDataDtoFactory = $monitoringDataDtoFactory;
        $this->monitoringDataRepository = $monitoringDataRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => ['pushDataToBoard', -20],
            GrowTilesEvent::EVENT_NAME => ['pushUpdatedDataToBoard', -20],
            DeleteMonitoringDataEvent::EVENT_DELETE_MONITORING_DATA => ['onDeleteMonitoringData', -20]
        ];
    }

    /**
     * @throws ZMQClientException
     * @throws UnexpectedValueException
     */
    public function onDeleteMonitoringData(DeleteMonitoringDataEvent $deleteMonitoringDataEvent): void
    {
        $this->monitoringDataDeletion->invoke($deleteMonitoringDataEvent->getMonitoringDataId());
    }

    /**
     * @throws Exception
     */
    public function pushDataToBoard(IncomingMonitoringDataEvent $incomingMonitoringDataEvent): void
    {
        $incomingMonitoringData = $incomingMonitoringDataEvent->getMonitoringData();
        $monitoringDataDocument = $this->monitoringDataRepository->find($incomingMonitoringData->getId());
        $this->monitoringDataRepository->getDocumentManager()->refresh($monitoringDataDocument);
        if (!$monitoringDataDocument) {
            throw new Exception(
                sprintf('Could not find Document for id %s in %s', $incomingMonitoringData->getId(), __METHOD__)
            );
        }
        $outgoingMonitoring = $this->monitoringDataDtoFactory->createOutgoingFromDocument(
            $monitoringDataDocument
        );
        $this->monitoringDataDtoPush->invoke($outgoingMonitoring);
    }

    public function pushUpdatedDataToBoard(GrowTilesEvent $event): void
    {
        $this->monitoringDataDocumentPush->invoke($event->getMonitorings());
    }
}
