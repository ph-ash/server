<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

class DeleteMonitoringDataFromBoardEvent extends Event implements DeleteMonitoringDataInterface
{
    public const EVENT_DELETE_MONITORING_FROM_BOARD_DATA = 'monitoring.delete_data.from_board';

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
