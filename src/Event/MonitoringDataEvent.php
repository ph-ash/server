<?php

declare(strict_types=1);

namespace App\Event;

use App\Dto\MonitoringData;

interface MonitoringDataEvent
{
    public function getMonitoringData(): MonitoringData;
}
