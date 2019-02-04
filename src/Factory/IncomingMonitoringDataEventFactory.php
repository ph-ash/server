<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\MonitoringData;
use App\Event\IncomingMonitoringDataEvent as Event;

class IncomingMonitoringDataEventFactory implements IncomingMonitoringDataEvent
{
    public function createFrom(MonitoringData $monitoringData): Event
    {
        return new Event($monitoringData);
    }
}
