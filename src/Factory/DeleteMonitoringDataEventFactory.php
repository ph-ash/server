<?php

declare(strict_types=1);

namespace App\Factory;

use App\Event\DeleteMonitoringDataEvent as Event;

class DeleteMonitoringDataEventFactory implements DeleteMonitoringDataEvent
{
    public function createFrom(string $monitoringDataId): Event
    {
        return new Event($monitoringDataId);
    }
}
