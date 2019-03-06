<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

class DeleteMonitoringDataEvent extends Event
{
    public const EVENT_DELETE_MONITORING_DATA = 'monitoring.delete_data';

    private $monitoringDataId;

    public function __construct(string $monitoringDataId)
    {
        $this->monitoringDataId = $monitoringDataId;
    }

    public function getMonitoringDataId(): string
    {
        return $this->monitoringDataId;
    }
}
