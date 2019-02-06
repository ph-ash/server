<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Dto\MonitoringData;

class BulkMonitoringData
{
    /**
     * @var MonitoringData[]
     */
    private $monitoringData;

    public function getMonitoringData(): array
    {
        return $this->monitoringData;
    }
}
