<?php

declare(strict_types=1);

namespace App\Event;

use App\Dto\MonitoringData;
use Symfony\Component\EventDispatcher\Event;

class DeleteMonitoringDataEvent extends Event
{
    public const EVENT_DELETE_MONITORING_DATA = 'monitoring.delete_data';

    private $monitoringData;

    public function __construct(MonitoringData $monitoringData)
    {
        $this->monitoringData = $monitoringData;
    }

    public function getMonitoringData(): MonitoringData
    {
        return $this->monitoringData;
    }
}
