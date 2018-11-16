<?php

namespace App\Factory;

use App\Dto\MonitoringData;
use App\Event\IncomingMonitoringDataEvent as Event;

interface IncomingMonitoringDataEvent
{
    public function create(MonitoringData $monitoringData): Event;
}
