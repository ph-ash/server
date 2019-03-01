<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\DeleteMonitoringDataEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Service\Validation\MonitoringDataValidation;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ValidationSubscriber implements EventSubscriberInterface
{
    private $monitoringDataValidation;

    public function __construct(MonitoringDataValidation $monitoringDataValidation)
    {
        $this->monitoringDataValidation = $monitoringDataValidation;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IncomingMonitoringDataEvent::EVENT_INCOMING_MONITORING_DATA => [
                ['validateMonitoringData', 0]
            ],
            DeleteMonitoringDataEvent::EVENT_DELETE_MONITORING_DATA => ['validateMonitoringData', -20]
        ];
    }

    /**
     * @throws Exception
     */
    public function validateMonitoringData(IncomingMonitoringDataEvent $incomingMonitoringDataEvent): void
    {
        try {
            $this->monitoringDataValidation->invoke($incomingMonitoringDataEvent->getMonitoringData());
        } catch (Exception $exception) {
            $incomingMonitoringDataEvent->stopPropagation();
            throw $exception;
        }
    }
}
