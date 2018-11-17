<?php

namespace App\Service;

use App\Dto\MonitoringData;

interface IncomingMonitoringDataDispatcher
{
    public function invoke(MonitoringData $monitoringData): void;
}
