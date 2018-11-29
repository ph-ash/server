<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MonitoringData;

interface IncomingMonitoringDataDispatcher
{
    public function invoke(MonitoringData $monitoringData): void;
}
