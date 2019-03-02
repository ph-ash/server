<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\MonitoringData;
use App\Event\IncomingMonitoringDataEvent as Event;

interface IncomingMonitoringDataEvent
{
    public function createFrom(MonitoringData $monitoringData): Event;
}
