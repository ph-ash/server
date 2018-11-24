<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Document\MonitoringData;
use App\Event\IncomingMonitoringDataEvent;
use Doctrine\ODM\MongoDB\DocumentManager;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistanceSubscriber implements EventSubscriberInterface
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public static function getSubscribedEvents(): array
    {
        //TODO add some priority
        return [IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => 'persistMonitoringData'];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function persistMonitoringData(IncomingMonitoringDataEvent $event): void
    {
        $monitoringDataDto = $event->getMonitoringData();

        $monitoringDataDocument = new MonitoringData(
            $monitoringDataDto->getId(),
            $monitoringDataDto->getStatus(),
            $monitoringDataDto->getPayload(),
            $monitoringDataDto->getPriority(),
            $monitoringDataDto->getIdleTimeoutInSeconds()
        );

        //TODO handle excepptions
        $this->documentManager->persist($monitoringDataDocument);
        $this->documentManager->flush($monitoringDataDocument);
    }
}
