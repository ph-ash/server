<?php

declare(strict_types=1);

namespace App\Service\Board;

use App\Dto\MonitoringData;

interface MonitoringDataPush
{
    public function invoke(MonitoringData $monitoringData);
}
