<?php

declare(strict_types=1);

namespace App\Event;

interface DeleteMonitoringDataInterface
{
    public function getMonitoringDataId(): string;
}
