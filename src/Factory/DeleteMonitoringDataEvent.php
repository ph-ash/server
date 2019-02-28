<?php

namespace App\Factory;

use App\Dto\MonitoringData;
use App\Event\DeleteMonitoringDataEvent as Event;

interface DeleteMonitoringDataEvent
{
    public function createFrom(MonitoringData $monitoringData): Event;
}
