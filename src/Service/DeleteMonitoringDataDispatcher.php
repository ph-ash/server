<?php

declare(strict_types=1);

namespace App\Service;

interface DeleteMonitoringDataDispatcher
{
    public function invoke(string $monitoringDataId): void;
}
