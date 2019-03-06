<?php

namespace App\Factory;

use App\Event\DeleteMonitoringDataEvent as Event;

interface DeleteMonitoringDataEvent
{
    public function createFrom(string $monitoringDataId): Event;
}
