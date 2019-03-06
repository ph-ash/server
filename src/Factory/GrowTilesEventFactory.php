<?php

declare(strict_types=1);

namespace App\Factory;

use App\Event\GrowTilesEvent as Event;
use App\Repository\MonitoringDataRepository;

class GrowTilesEventFactory implements GrowTilesEvent
{
    private $monitoringDataRepository;

    public function __construct(MonitoringDataRepository $monitoringDataRepository)
    {
        $this->monitoringDataRepository = $monitoringDataRepository;
    }

    public function create(): Event
    {
        $erroneousMonitorings = $this->monitoringDataRepository->findAllErroneousMonitorings();
        return new Event($erroneousMonitorings);
    }
}
