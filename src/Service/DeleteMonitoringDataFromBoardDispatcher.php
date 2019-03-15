<?php

declare(strict_types=1);

namespace App\Service;

interface DeleteMonitoringDataFromBoardDispatcher
{
    public function invoke(string $monitoringDataId): void;
}
