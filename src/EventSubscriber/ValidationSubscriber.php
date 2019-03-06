<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\DeleteMonitoringDataEvent;
use App\Event\IncomingMonitoringDataEvent;
use App\Event\MonitoringDataEvent;
use App\Service\Validation\MonitoringDataValidation;
use Exception;
use RuntimeException;
use Symfony\Component\EventDispatcher\Event;
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
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public function validateMonitoringData(IncomingMonitoringDataEvent $incomingMonitoringDataEvent): void
    {
        $this->monitoringDataValidation->invoke($incomingMonitoringDataEvent->getMonitoringData());
    }
}
