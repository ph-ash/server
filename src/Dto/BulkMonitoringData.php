<?php

declare(strict_types=1);

namespace App\Dto;

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
