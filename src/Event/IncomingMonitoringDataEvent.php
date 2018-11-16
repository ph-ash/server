<?php

declare(strict_types=1);

namespace App\Event;

use App\Dto\MonitoringData;
use Symfony\Component\EventDispatcher\Event;

class IncomingMonitoringDataEvent extends Event
{
    public const EVENT_INCOMING_MONITORING_DATA = 'monitoring.incoming_data';

    private $monitoringData;

    public function __construct(MonitoringData $monitoringData)
    {
        $this->monitoringData = $monitoringData;
    }

    public function getId(): string
    {
        $this->monitoringData->getId();
    }

    public function getStatus(): string
    {
        return $this->monitoringData->getStatus();
    }

    public function getPayload(): string
    {
        return $this->monitoringData->getPayload();
    }

    public function getIdleTimeoutInMinutes(): int
    {
        return $this->monitoringData->getIdleTimeoutInMinutes();
    }
}
