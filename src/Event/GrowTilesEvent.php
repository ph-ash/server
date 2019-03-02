<?php

declare(strict_types=1);

namespace App\Event;

use App\Document\MonitoringData;
use Symfony\Component\EventDispatcher\Event;

class GrowTilesEvent extends Event
{
    public const EVENT_NAME = 'monitoring.grow-tiles';

    /** @var MonitoringData[] */
    private $monitorings;

    public function __construct(array $monitorings)
    {
        $this->monitorings = $monitorings;
    }

    /** @return MonitoringData[] */
    public function getMonitorings(): array
    {
        return $this->monitorings;
    }

    /** @var MonitoringData[] $monitorings */
    public function setMonitorings(array $monitorings): void
    {
        $this->monitorings = $monitorings;
    }
}
