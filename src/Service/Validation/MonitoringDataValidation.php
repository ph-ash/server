<?php

declare(strict_types=1);

namespace App\Service\Validation;

use App\Dto\MonitoringData;

interface MonitoringDataValidation
{
    /**
     * TODO throw validation exception
     */
    public function invoke(MonitoringData $monitoringData);
}
