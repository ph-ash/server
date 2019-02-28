<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\MonitoringData;
use App\Event\DeleteMonitoringDataEvent as Event;

class DeleteMonitoringDataEventFactory implements DeleteMonitoringDataEvent
{
    public function createFrom(MonitoringData $monitoringData): Event
    {
        return new Event($monitoringData);
    }
}
