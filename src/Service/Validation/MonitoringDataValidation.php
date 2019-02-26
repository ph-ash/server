<?php

declare(strict_types=1);

namespace App\Service\Validation;

use App\Dto\MonitoringData;
use App\Exception\ValidationException;
use OutOfBoundsException;

interface MonitoringDataValidation
{
    /**
     * @throws ValidationException
     * @throws OutOfBoundsException
     */
    public function invoke(MonitoringData $monitoringData);
}
