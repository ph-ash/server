<?php

declare(strict_types=1);

namespace App\Factory;

use App\Event\DeleteMonitoringDataEvent as Event;

interface DeleteMonitoringDataEvent
{
    public function createFrom(string $monitoringDataId): Event;
}
